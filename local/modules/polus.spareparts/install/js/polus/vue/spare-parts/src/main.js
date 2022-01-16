import {Vue} from "ui.vue";
import Parts from "./components/parts.vue";
import Point from "./components/point.vue";
import "polus.vue.plugins.request";
import "polus.vue.plugins.notification";
import "polus.vue.plugins.confirm";
import "./style.scss";

Vue.component("Point", Point);
Vue.component("Parts", Parts);