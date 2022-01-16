"use strict"

import {Vue} from "ui.vue";
import {MessageBox} from 'ui.dialogs.messagebox';

let Confirm = function() {
    this.okButtonName = 'Ok';
    this.title = '';
}

Confirm.prototype.setOkButtonName = function(name) {
    this.okButtonName = name;
    return this;
}

Confirm.prototype.setTitle = function(title) {
    this.title = title;
    return this;
}

Confirm.prototype.open = function(message, callback) {
    let self = this;
    BX.UI.Dialogs.MessageBox.confirm(
        message,
        self.title,
        (messageBox) => {
            if (callback.hasOwnProperty('onSuccess')) {
                callback.onSuccess();
            }

            messageBox.close();
        },
        self.okButtonName,
        (messageBox) => {
            if (callback.hasOwnProperty('onCancel')) {
                callback.onCancel();
            }

            messageBox.close();
        }
    );
}

Confirm.install = function(Vue, options) {
    Vue.prototype.$confirm = new Confirm();
}

Vue.use(Confirm);

export default Confirm;