import './style.scss';

export let SpareParts = {
    name: 'SpareParts',
    data() {
        return {
            currentSparePartId: 0,
            currentElement: {},
            currentElementShow: false
        }
    },
    methods: {
        /**
         * Возвращает координаты точки на схеме
         * @param x
         * @param y
         * @returns {string}
         */
        getStyle(x, y) {
            return 'left:' + x + 'px;top:' + y + 'px;';
        },
        choosePoint(sparePartId) {
            this.currentSparePartId = sparePartId;

            const {id} = this.currentElement;

            if (id && parseInt(sparePartId) === parseInt(id)) {
                return;
            }

            this.currentElementShow = false;
            this.$request
                .setUrl(this.componentName)
                .setSignedParameters(this.signedParameters)
                .setData({elementId: sparePartId, sessid: this.sessid})
                .send(this.methodName)
                .then(response => {
                    this.currentElement = response;
                    this.currentElementShow = true;
                });
        }
    }
}

export default {SpareParts}