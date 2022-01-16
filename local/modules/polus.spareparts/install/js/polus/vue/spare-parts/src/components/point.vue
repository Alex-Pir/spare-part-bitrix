<template>
    <tr class="element-point">
      <td><input :class="{'error': !elementId}" size="3" type="text" name="'index'" v-model="elementId" @change="changeId"></td>
      <td><input
          :class="{'error': !value}"
          :name="'PROP[SPARE_PART][n' + id + ']'"
          :id="'PROP[SPARE_PART][n' + id + ']'"
          size="3"
          type="text"
          ref="point"
          v-model="value"
          readonly
      ></td>
      <td><input size="3" type="text" name="'coords-x'" readonly v-model="coords.x"></td>
      <td><input size="3" type="text" name="'coords-y'" readonly v-model="coords.y"></td>
      <td><input type="button" value="..." @click.prevent="addElement"></td>
      <td><input type="button" value="x" @click.prevent="removeElement"></td>
    </tr>
</template>

<script>
export default {
  name: "point",
  data() {
    return {
      active: false,
      value: this.allowValue,
      elementId: this.id
    }
  },
  props: {
    id: {
      type: [Number, String],
      default: 0
    },
    iblockId: {
      type: Number,
      default: 0
    },
    allowValue: {
      type: [Number, String],
      default: ''
    },
    allowIndexes: {
      type: Array,
      default: []
    },
    error: {
      type: Boolean,
      default: false
    },
    coords: {
      type: Object,
      default: function() {
        return {
          x: '',
          y: ''
        };
      }
    }
  },
  watch: {
    allowValue(val) {
      this.value = val;
    },
    id(val) {
      this.elementId = val;
    }
  },
  methods: {
    changeId() {
      if (!this.elementId.match(/^\d+$/)) {
        this.elementId = this.id;
        return;
      }

      const index = this.allowIndexes.findIndex(item => item === this.elementId);

      if (index === -1) {
        this.$emit('changeId', {id: this.elementId, oldId: this.id});
      } else {
        this.elementId = this.id;
      }

    },
    addElement() {
      jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=ru&IBLOCK_ID=' + this.iblockId + '&n=PROP[SPARE_PART]&k=n' + this.id + '&iblockfix=y', 900, 700);
    },
    removeElement() {
      this.$emit('remove', this.id);
    },
    getElement() {
      let element = document.getElementById('PROP[SPARE_PART][n' + this.id + ']');
      return {id: this.elementId, x: this.coords.x, y: this.coords.y, value: element.value};
    },
    setActive(active) {
      this.active = active;
    }
  }
}
</script>