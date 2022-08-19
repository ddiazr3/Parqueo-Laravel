/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

import Vue from "vue";
import excel from "vue-excel-export"
window.Vue = require("vue").default;
Vue.use(excel)
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context("./", true, /\.vue$/i);
files
    .keys()
    .map((key) =>
        Vue.component(key.split("/").pop().split(".")[0], files(key).default)
    );

Vue.component("dashboard",require("./views/Dashboard.vue").default)

//componentes de componentes
Vue.component("data-table", require("./components/dataTable.vue").default)
Vue.component("InputField",require("./components/InputField.vue").default);
Vue.component("BarChart",require("./components/Chart/BarChart.vue").default);
Vue.component("DoughnutChart",require("./components/Chart/DoughnutChart.vue").default);
//componentes de reportes
Vue.component("reports-general", require("./views/reports/General.vue").default);

//componentes de catalogos
Vue.component("catalogs-empresas-edit",require("./views/catalogs/EmpresasEdit.vue").default);
Vue.component("catalogs-roles-edit",require("./views/catalogs/RolesEdit.vue").default);
Vue.component("catalogs-rolemodule",require("./views/catalogs/RoleModule.vue").default);
Vue.component("catalogs-users-edit", require("./views/catalogs/UsersEdit.vue").default);
Vue.component("profile",require("./views/Profile.vue").default);
Vue.component("registrar",require("./views/Registrar.vue").default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
});
