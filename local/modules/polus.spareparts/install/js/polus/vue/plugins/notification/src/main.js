"use strict"

import {Vue} from "ui.vue";
import "ui.notification";
import "ui.notification.balloon";

let Notification = function() {
    this.success = true;
}

Notification.prototype.setError = function() {
    this.success = false;
    return this;
}

Notification.prototype.setSuccess = function() {
    this.success = true;
    return this;
}

Notification.prototype.open = function(message) {
    let self = this;
    BX.UI.Notification.Center.notify({
        content: message,
        autoHideDelay: 5000,
        closeButton: true,
        render: function() {
            return BX.create("div", {
                props: {
                    className: "message-notification"
                },
                style: {
                    'background-color': self.success ? "#488a00" : "#fd0202"
                },
                children: [
                    BX.create('div',{
                        html: this.getContent()
                    }),
                    this.getCloseButton()
                ]
            })
        },
    });
}

Notification.install = function(Vue, options) {
    Vue.prototype.$notification = new Notification();
}

Vue.use(Notification);

export default Notification;