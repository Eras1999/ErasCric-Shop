import $ from 'jquery';

class BaseSettings {
    constructor({id, prefix, data}) {
        this.id = id;
        this.prefix = prefix;
        this.data = data;
        $(document.body).on('change', '[name^="woocommerce_ppcp_"]', this.handleInputChange.bind(this));
        $(document.body).on('click', '.wc-ppcp-clipboard', this.handleClipboard.bind(this));
        this.maybeShowDependencies();
    }

    getFieldKey(key) {
        return `${this.prefix}${key}`;
    }

    getFieldValue(key) {
        const $el = $(`#${this.getFieldKey(key)}`);
        if ($el.is(':checkbox')) {
            return $el.is(':checked');
        }
        return $el.val();
    }

    setFieldValue(key, value) {
        $(`#${this.getFieldKey(key)}`).val(value);
    }

    handleInputChange(e) {
        this.maybeShowDependencies();
        if ($(e.currentTarget).attr('name') === 'woocommerce_ppcp_use_place_order') {
            if ($(e.currentTarget).is(':checked')) {
                $('#woocommerce_ppcp_checkout_placement').val('place_order').trigger('change');
            }
        }
    }

    /**
     * Shows dependency settings based on value
     */
    maybeShowDependencies() {
        const elements = document.querySelectorAll('[data-show-if]');
        const hiddenElements = [];
        for (const element of elements) {
            const attr = element.getAttribute('data-show-if');
            const matches = attr.match(/(([\w]+)=([\w]+))+/g);
            if (matches && !hiddenElements.includes(element.id)) {
                for (const match of matches) {
                    const [key, value] = match.split('=');
                    const el = document.getElementById(`${this.prefix}${key}`);
                    try {
                        if ($(el).is(':checkbox')) {
                            if (el.checked.toString() != value) {
                                throw 'no match';
                            }
                        } else {
                            if (el.value != value) {
                                throw 'no match';
                            }
                        }
                        $(element).closest('tr').show();
                    } catch (err) {
                        hiddenElements.push(element.id);
                        $(element).closest('tr').hide();
                    }
                }
            }
        }
    }

    getSettings() {
        return wcPPCPSettings[this.id];
    }

    getLoaderHtml() {
        return `<div class="wc-ppcp-loader">
            <div></div>
            <div></div>
            <div></div>
        </div>`;
    }

    showLoader(parent) {
        $(parent).append(this.getLoaderHtml());
    }

    hideLoader(parent) {
        $(parent).find('.wc-ppcp-loader').remove();
    }

    block(el = null, args = {}) {
        const defaults = {
            css: {
                border: 'none',
                'background-color': 'transparent',
                'font-size': '16px'
            },
            overlayCSS: {
                background: '#fff',
                opacity: 0.7
            }, ...args
        }
        if (el) {
            $(el).block(defaults);
        } else {
            $.blockUI(defaults);
        }
    }

    unblock(el) {
        if (el) {
            $(el).unblock();
        } else {
            $.unblockUI();
        }
    }

    handleClipboard(e) {
        const value = $(e.currentTarget).parent().data('clipboard');
        const el = document.createElement('input');
        el.value = value;
        el.type = 'text';
        $(document.body).append(el);
        el.select();
        document.execCommand('copy');
        $(el).remove();
    }

    getAjaxPath(path) {
        const ajaxUrl = wcPPCPSettings?.adminAjaxUrl || '';
        path = path.replace(/(^\/)/, '');
        return ajaxUrl.replace('$path', path);
    }
}

export default BaseSettings;