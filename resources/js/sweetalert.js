import VueSweetAlert from 'vue-sweetalert'
Vue.use(VueSweetAlert)

var SweetAlert = {
    methods: {
        alert(context, options) {
            context.$swal(options);
        },
        alertSuccess(context, {title = "Confirmación", text = "Todo correcto", timer = 10000,
            showConfirmationButton = false} = {}) {
            this.alert(context, {
                title: title,
                text: text,
                timer: timer,
                showConfirmButton: showConfirmationButton,
                type: 'success'
            });
        },
        alertWarning(context, {title = "", text = ""} = {}) {
            this.alert(context, {
                title: title,
                text: text,
                type: 'warning'
            });
        },
        alertError(context, {title = "Error", text = "Hubo un error"} = {}) {
            this.alert(context, {
                title: title,
                text: text,
                type: 'error'
            });
        },
        alertInfo(context, {title = "Info", text = ""} = {}) {
            this.alert(context, {
                title: title,
                text: text,
                type: 'info'
            });
        },
        confirm(context, options) {
            options = Object.assign({
                title: "Confirmación",
                text: "¿Está seguro?",
                type: "warning",
                showCancelButton: true,
                allowOutsideClick : false,
                confirmButtonText: "Si",
                cancelButtonText: "No",
            }, options);
            return context.$swal(options);
        }
    }
};
export default SweetAlert;
