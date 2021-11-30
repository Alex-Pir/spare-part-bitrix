import {Vue} from 'ui.vue';

export class VueInit {
    constructor(component, el, params) {
        this.component = component;
        this.component.el = el;
        this.initData(params);
    }

    initData(params) {
        let oldData = this.component.data();

        params = Object.assign({}, params, oldData);

        this.component.data = function() {
            return params;
        }
    }

    init() {
        if (this.component.el.trim() === '') {
            return;
        }

        console.log(this.component);

        Vue.create(this.component);
    }
}