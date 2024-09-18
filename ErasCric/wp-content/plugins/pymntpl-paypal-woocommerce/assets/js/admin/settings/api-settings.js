import $ from 'jquery';
import apiFetch from "@wordpress/api-fetch";
import BaseSettings from "./base-settings";
import {registerSettings} from "./register";

const OAUTH_DATA = 'WC_PPCP_OAUTH_DATA';

class ApiSettings extends BaseSettings {
    constructor(params) {
        super(params);
        setSettings(this);
        this.redirecting = false;
        $(document.body).on('click', '.delete-connected-account', this.deleteConnection.bind(this));
        $(document.body).on('click', '.create-webhook', this.createWebhook.bind(this));
        $(document.body).on('change', '#woocommerce_ppcp_api_environment', this.handleEnvironmentChange.bind(this))

        this.onInit();
    }

    async onInit() {
        await this.doConnectionInitialization();
        this.handleEnvironmentChange();
        this.initializeConnectionButtons();
    }

    async doConnectionInitialization() {
        const data = this.getOAuthData();
        if (data) {
            this.handleOAuthSetup(data);
        }
    }

    handleEnvironmentChange() {
        const value = $('#woocommerce_ppcp_api_environment').val();
        const showClass = `.show_if_${value}`;
        const hideClass = `.hide_if_${value}`;
        $(showClass).show();
        $(hideClass).hide();
    }

    async initializeConnectionButtons() {
        for (const environment of ['sandbox', 'production']) {
            let el = document.getElementById(`woocommerce_ppcp_api_connect_${environment}`);
            el.setAttribute('data-paypal-onboard-complete', 'ppcpOnBoardCallback');
        }
        await this.loadPartnerScript();
    }

    async loadPartnerScript() {
        return new Promise(resolve => {
            const script = document.createElement('script');
            script.id = 'paypal-js';
            script.src = this.getScriptSrc();
            script.async = true;
            script.onload = () => resolve();
            document.head.appendChild(script);
        });
    }

    getScriptSrc() {
        return 'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js';
    }

    supportsLocalStorage() {
        return 'localStorage' in window;
    }

    async submitOAuthData(authCode, sharedId, environment = null) {
        if (this.isProcessingOnboarding) {
            return;
        }
        try {
            this.isProcessingOnboarding = true;
            environment = !environment ? $(`#${this.getFieldKey('environment')}`).val() : environment;
            let result = await apiFetch({
                keepalive: true,
                url: this.getAjaxPath('wc-ppcp/v1/admin/account/connect'),
                method: 'PUT',
                data: {
                    environment,
                    authCode,
                    sharedId
                }
            });
            this.isProcessingOnboarding = false;
            return result;
        } catch (error) {
            this.isProcessingOnboarding = false;
            return error;
        }
    }

    async handleOnboardCallback(authCode, sharedId) {
        if (this.supportsLocalStorage()) {
            this.storeOAuthData({
                environment: $(`#${this.getFieldKey('environment')}`).val(),
                authCode,
                sharedId
            });
            window.onbeforeunload = '';
            if (PAYPAL?.apps?.Signup?.MiniBrowser?.closeFlow) {
                PAYPAL.apps.Signup.MiniBrowser.closeFlow();
                this.doConnectionInitialization();
            } else {
                clearInterval(this.redirectInterval);
                this.redirectInterval = setInterval(() => {
                    if (PAYPAL?.apps) {
                        if (!PAYPAL.apps.Signup.MiniBrowser.isOpen()) {
                            setTimeout(async () => {
                                if (!this.redirecting) {
                                    clearInterval(this.redirectInterval);
                                    this.rejectRedirect();
                                }
                            }, 3000);
                            this.block();
                        }
                    }
                }, 1000);
            }
        }
    }

    async handleOAuthSetup(data) {
        const {environment, authCode, sharedId} = data;
        this.block(this.data.messages.connecting);
        const result = await this.submitOAuthData(authCode, sharedId, environment);
        if (result.code) {
            this.unblock();
            window.alert(result.message);
        } else {
            // display success message
            window.location.reload();
        }
        this.removeOAuthData();
    }

    storeOAuthData(data) {
        if (this.supportsLocalStorage()) {
            localStorage.setItem(OAUTH_DATA, JSON.stringify(data));
        }
    }

    getOAuthData() {
        if (this.supportsLocalStorage()) {
            return JSON.parse(localStorage.getItem(OAUTH_DATA));
        }
    }

    removeOAuthData() {
        if (this.supportsLocalStorage()) {
            localStorage.removeItem(OAUTH_DATA);
        }
    }

    async createWebhook(e) {
        e.preventDefault();
        const $button = $('.create-webhook');
        $button.prop('disabled', true);
        this.showLoader('.create-webhook');
        try {
            const result = await apiFetch({
                url: this.getAjaxPath('/wc-ppcp/v1/admin/webhook'),
                method: 'PUT',
                data: {environment: $('#woocommerce_ppcp_api_environment').val()}
            });
            $(`#woocommerce_ppcp_api_webhook_id_${result.environment}`).val(result.id);
            $button.prop('disabled', false);
            window.alert(result.message);
        } catch (error) {
            $button.prop('disabled', false);
            window.alert(error.message);
        } finally {
            this.hideLoader('.create-webhook');
        }
    }

    block(message = null) {
        $('body').addClass('vx_has-spinner vx_has-spinner-large');
        if (message) {
            $('body').append(`<div class="vx_spinner-message">${message}</div>`);
        }
    }

    unblock() {
        $('body').removeClass(['vx_has-spinner vx_has-spinner-large']);
        $('body').find('.vx_spinner-message').remove();
    }

    async deleteConnection(e) {
        e.preventDefault();
        if (!confirm(this.data?.messages?.confirmDeleteConnection)) {
            return;
        }
        this.showLoader('.delete-connected-account');
        $('.delete-connected-account').prop('disabled', true);
        try {
            const result = await apiFetch({
                url: this.getAjaxPath('wc-ppcp/v1/admin/account/connect'),
                method: 'DELETE',
                data: {environment: $(`#${this.getFieldKey('environment')}`).val()}
            });
            if (result.code) {
                $('.delete-connected-account').prop('disabled', false);
                window.alert(result.message);
            } else {
                window.location.reload();
            }
        } catch (error) {
            $('.delete-connected-account').prop('disabled', false);
            window.alert(error.message);
        } finally {
            this.hideLoader('.delete-connected-account');
        }
    }

    setRedirecting(bool) {
        this.redirecting = true;
    }
}

let settings;
const setSettings = (value) => {
    settings = value;
}

window.ppcpOnBoardCallback = (...args) => {
    settings.handleOnboardCallback(...args);
    $(window).on('beforeunload', () => settings.setRedirecting(true));
}

registerSettings('ppcp_api', ApiSettings);

