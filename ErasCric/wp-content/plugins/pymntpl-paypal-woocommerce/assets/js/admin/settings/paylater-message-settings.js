import BaseSettings from "./base-settings";
import {registerSettings} from "./register";
import $ from 'jquery';

class PayLaterMessageSettings extends BaseSettings {

}

registerSettings('ppcp_paylater_message', PayLaterMessageSettings);