<template>
  <div class="parts-container">
    <div class="container-image">
      <div class="container-image__block">
        <input type="file" id="spare-part-picture" name="file" v-model="file" accept="image/*" @change="sendFile" v-if="!picture.src">
        <img :src="picture.src" @click="setPoint" v-else />
        <input class="picture-remove" type="submit" value="x" @click.prevent="removeFile" v-if="picture.src">
      </div>
      <div
          v-for="element in points"
          :key="element.id"
          class="container-point"
          :style="getStyle(element.x, element.y)"
          v-html="element.id"
      >
      </div>
    </div>
    <div class="parts-container__points" v-if="picture.src && points.length > 0">
      <table>
        <thead>
        <th v-html="loc.POLUS_VUE_SPARE_PART_TABLE_HEADER_INDEX"></th>
        <th v-html="loc.POLUS_VUE_SPARE_PART_TABLE_HEADER_ELEMENT"></th>
        <th v-html="loc.POLUS_VUE_SPARE_PART_TABLE_HEADER_X"></th>
        <th v-html="loc.POLUS_VUE_SPARE_PART_TABLE_HEADER_Y"></th>
        <th></th>
        <th></th>
        </thead>
        <tbody>
          <point
              v-for="element in points"
              :key="'spare-part-' + element.id"
              :id="element.id"
              :iblock-id="sparePartsIblockId"
              :coords="{x: element.x, y: element.y}"
              :allow-value="element.value"
              :error="element.error"
              :allow-indexes="indexes"
              @changeId="setPointId"
              @remove="removePoint"
          ></point>
        </tbody>
      </table>
      <input type="submit" @click.prevent="sendForm" />
    </div>
  </div>

</template>

<script>
import {Vue} from "ui.vue";

export default {
  name: "parts",
  data() {
    return {
      points: this.savedPoints,
      indexes: [],
      lastId: 0,
      file: null,
      fileUrl: 'polus:spareparts.spartfile.',
      partsUrl: 'polus:spareparts.iblock.'
    }
  },
  props: {
    picture: {
      type: Object,
      default: function () {
        return {
          src: '',
          id: 0
        }
      }
    },
    savedPoints: {
      type: Array,
      default: []
    },
    productIblockId: {
      type: Number,
      default: 0
    },
    sparePartsIblockId: {
      type: Number,
      default: 0
    },
    elementId: {
      type: Number,
      default: 0
    },
    rewriteIfRemove: {
      type: Boolean,
      default: true
    }
  },
  mounted() {
    for (let i in this.points) {
      this.indexes.push(this.points[i].id);
      if (parseInt(this.lastId) < parseInt(this.points[i].id)) {
        this.lastId = parseInt(this.points[i].id);
      }
    }
  },
  computed: {
    loc() {
      return Vue.getFilteredPhrases("POLUS_VUE_");
    }
  },
  methods: {
    /**
     * Добавляет точку на схему
     * @param event
     */
    setPoint(event) {
      const x = event.offsetX - 10;
      const y = event.offsetY - 10;

      /**
       * если привязать элемент инфоблока к точке, а после
       * добавить новую точку, то несохраненные точки сбросят свои привязки
       * к элементам инфоблока. Этот цикл позволяет этого избежать
       */
      for (let i in this.points) {
        if (this.points[i].value.trim() === '') {
          this.$set(this.points[i], 'value', this.getSparePartInputValue(this.points[i].id));
        }
      }

      this.indexes.push(++this.lastId);
      this.points.push({id: this.lastId, x: x, y: y, value: '', error: false});
    },
    /**
     * Возвращает координаты точки на схеме
     * @param x
     * @param y
     * @returns {string}
     */
    getStyle(x, y) {
      return 'left:' + x + 'px;top:' + y + 'px;';
    },
    /**
     * Возвращает идентификатор связанной запчасти по ее порядковому номеру/идентификатору
     * @param id
     * @returns {string|*}
     */
    getValue(id) {
      let element = this.points.find(item => item.id === id);

      if (element && element.hasOwnProperty('value')) {
        return element.value;
      }

      return '';
    },
    /**
     * Изменение порядкового номера/идентификатора запчасти
     * @param value
     */
    setPointId(value) {
      if (!value.hasOwnProperty("oldId")) {
        return;
      }

      let index = this.points.findIndex(item => item.id === value.oldId);
      let indexIssetElement = this.points.findIndex(item => item.id === value.id);

      if (indexIssetElement !== -1 || index === -1) {
        return;
      }

      let indexFromSpecialArray = this.indexes.findIndex(item => item === value.oldId);

      if (indexFromSpecialArray !== -1) {
        this.$set(this.indexes, indexFromSpecialArray, index);
      }

      delete value.oldId;

      value.x = this.points[index].x;
      value.y = this.points[index].y;
      value.value = this.points[index].value;

      this.$set(this.points, index, value);

    },
    /**
     * Удаление запчасти
     * @param id
     */
    removePoint(id) {
      const elementIndex = this.points.findIndex(item => item.id === id);
      this.points.splice(elementIndex, 1);

      if (this.rewriteIfRemove) {
        for (let i in this.points) {

          if (this.points[i].value.trim() === '') {
            this.$set(this.points[i], 'value', this.getSparePartInputValue(this.points[i].id));
          }

          this.lastId = this.points[i].id = parseInt(i) + 1;
        }
      }
    },
    /**
     * Сохранение файла со схемой запчастей
     */
    sendFile() {
      let formSendAll = new FormData();
      let file = document.getElementById('spare-part-picture').files[0];

      if (!file) {
        return;
      }

      formSendAll.append('file', file);
      formSendAll.append('iblockId', this.productIblockId.toString());
      formSendAll.append('elementId', this.elementId.toString());

      this.$request
          .setUrl(this.fileUrl + 'saveFile')
          .setData(formSendAll)
          .send()
          .then(response => {
            const {src, id} = response;

            if (src && id) {
              this.$set(this.picture, 'src', response.src);
              this.$set(this.picture, 'id', response.id);
            }

          });
    },
    /**
     * Удаление схемы запчастей
     */
    removeFile() {
      this.$confirm
          .setOkButtonName(this.loc.POLUS_VUE_REMOVE_BUTTON_OK)
          .setTitle(this.loc.POLUS_VUE_REMOVE_SPARE_PARTS_TITLE)
          .open(
              this.loc.POLUS_VUE_REMOVE_SPARE_PARTS_CONFIRM,
              {
                onSuccess: this.removeFileSuccess,
                onCancel: this.removeFileCancel
              }
          );
    },
    /**
     * Обработчик подтверждения удаления схемы запчастей
     */
    removeFileSuccess() {
      let fileFormData = new FormData();

      fileFormData.append('fileId', this.picture.id)

      this.$request
          .setUrl(this.fileUrl + 'removeFile')
          .setData(fileFormData)
          .send()
          .then(response => {
            if (response === true) {
              this.file = null;
              this.$set(this.picture, 'src', '');
              this.$set(this.picture, 'id', 0);
              this.points.splice(0);
              this.lastId = 0;

              this.showSuccessMessage(this.loc.POLUS_VUE_FILE_SPARE_PART_REMOVE_SUCCESS);
            }
          });
    },
    /**
     * Обработчик отмены удаления схемы запчастей
     * @returns {boolean}
     */
    removeFileCancel() {
      return false;
    },
    /**
     * Отправляет форму с запчастями на сервер
     * @returns {boolean}
     */
    sendForm() {
      this.saveSparePartsToArray();

      if (!this.isValid()) {
        return false;
      }

      let formSendAll = new FormData();

      let items = {};
      for (let i in this.points) {
        items[this.points[i].id] = this.points[i];
      }

      let parameters = {
        items: JSON.stringify(items),
        iblock_id: this.sparePartsIblockId,
        element_id: this.elementId,
        file: this.picture.src
      };

      formSendAll.append('parameters', JSON.stringify(parameters));

      this.$request
        .setUrl(this.partsUrl + 'saveSparePart')
        .setData(formSendAll)
        .send()
        .then(response => {
          if (response === true) {
            this.showSuccessMessage(this.loc.POLUS_VUE_SAVE_SUCCESS);
          } else {
            this.showErrorMessage(this.loc.POLUS_VUE_SAVE_ERROR);
          }
        })
        .catch(error => {
          console.error(error);
          this.showErrorMessage(this.loc.POLUS_VUE_SAVE_ERROR);
        });
    },
    /**
     * Проверяет заполнены ли все обязательные поля
     * @returns {boolean}
     */
    isValid() {
      for (let i in this.points) {
        if (this.points[i].value.trim() === '') {

          this.showErrorMessage(this.loc.POLUS_VUE_SEND_VALID_ERROR);

          this.points[i].error = true;
          return false;
        }

        this.points[i].error = false;
      }

      return true;
    },
    /**
     * Сохранение значений запасных частей в массив
     */
    saveSparePartsToArray() {
      for (let i in this.points) {
        let value = this.getSparePartInputValue(this.points[i].id);
        this.$set(this.points[i], 'value', value);
      }
    },
    /**
     * Возвращает идентификатор товара из поля запасных частей
     * @param pointId
     * @returns {*}
     */
    getSparePartInputValue(pointId) {
      return document.getElementById('PROP[SPARE_PART][n' + pointId + ']').value;
    },
    /**
     * Выводит сообщение об успешности операции
     * @param message
     */
    showSuccessMessage(message) {
      this.$notification
          .setSuccess()
          .open(message);
    },
    /**
     * Выводит сообщение об ошибке
     * @param message
     */
    showErrorMessage(message) {
      this.$notification
          .setError()
          .open(message);
    }
  }
}
</script>