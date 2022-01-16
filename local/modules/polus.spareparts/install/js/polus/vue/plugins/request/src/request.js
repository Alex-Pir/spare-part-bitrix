"use strict"

import {Vue} from "ui.vue";

let Request = function() {
    this.data = null;
    this.signedParameters = null;
    this.options = null;
    this.url = null;
};

Request.prototype.setSignedParameters = function(parameters) {
    this.signedParameters = parameters;
    return this;
}

Request.prototype.setData = function(data) {
    this.data = data;
    return this;
}

Request.prototype.setOptions = function(options) {
    this.options = options;
    return this;
}

Request.prototype.setUrl = function(url) {
    this.url = url;
    return this;
}

Request.prototype.send = function(componentMethod = null) {
    let url = this.url;
    let data = this.data;

    return new Promise((resolve, reject) => {
        let result = componentMethod !== null
            ? this.sendToComponent(url, data, componentMethod)
            : this.sendToModule(url, data);

        result.then(response => {
            if (response.status === "success" && response.hasOwnProperty("data")) {
                resolve(response.data);
            } else {
                reject(response);
            }
        })
        .catch(error => {
            if (error.hasOwnProperty("errors")) {
                let resultMessage = [];
                const errors = error.errors;

                for (let i in errors) {
                    resultMessage.push(errors[i].message);
                }

                reject(resultMessage.join('</br>'));
            } else {
                reject(error);
            }
        });
    });
}

Request.prototype.sendToModule = function(url, data) {
    return BX.ajax.runAction(
        url,
        {
            data: data,
            preparePost: true,
            enctype: 'multipart/form-data'
        }
    );
}

Request.prototype.sendToComponent = function(url, data, componentMethod) {
    return BX.ajax.runComponentAction(
        url,
        componentMethod,
        {
            data: data,
            mode: 'class',
            //enctype: 'multipart/form-data',
            signedParameters: this.signedParameters
        }
    );
}

Request.install = function(Vue, options) {
    Vue.prototype.$request = new Request();
}

Vue.use(Request);

export default Request;