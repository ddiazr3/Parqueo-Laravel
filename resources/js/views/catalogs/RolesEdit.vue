<template>
    <div class="card">
        <div v-if="loading" class="text-center">
            <div class="card-body">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
            </div>
        </div>
        <template v-else>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-sm-12 mb-2">
                        <label for="nombre">Nombre</label>
                        <input name="nombre" type="text" :class="errorClass('role.name')" v-model="data.role.name"/>
                        <small class="text-danger" v-if="validationErrors['role.name']">
                            {{ validationErrors["role.name"][0] }}
                        </small>
                    </div>
                    <div class="form-group col-sm-12 mb-2">
                        <label for="descripcion">Descripción</label>
                        <input
                            name="descripcion"
                            type="text"
                            :class="errorClass('role.description')"
                            v-model="data.role.description"
                        />
                        <small class="text-danger" v-if="validationErrors['role.description']">
                            {{ validationErrors["role.description"][0] }}
                        </small>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <div class="form-group mb-2">
                            <label for="roles">Empresas</label>
                            <selectize
                                name="empresas"
                                :class="{
                                    'form-control': true,
                                }"
                                v-model="data.role.empresas_ids"
                                multiple
                            >
                                <option v-for="empresa in data.empresas" :value="empresa.id">
                                    {{ empresa.empresa }}
                                </option>
                            </selectize>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="data.role.ver_dashboard">
                            <label class="custom-control-label" for="customSwitch1" >Ver Dashboar</label>
                        </div>
                    </div>
                </div>

                <label>Permisos</label>
                <div class="row">
                    <div v-for="(mp, title) in data.modulepermissions" :key="mp.name" class="col-sm-4">
                        <catalogs-rolemodule
                            :modulepermissions="mp"
                            :title="title"
                            :rolemodulepermissions="data.role.role_module_permissions"
                        />
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" @click="save" :disabled="saving">Guardar</button>
            </div>
        </template>
    </div>
</template>

<script>
import axios from "axios";
import Selectize from "vue2-selectize";
import Sweetalert from "../../sweetalert";

export default {
    mixins: [Sweetalert],
    data() {
        return {
            data: {
                role: null,
                modules: [],
                modulepermissions: [],
            },
            loading: true,
            saving: false,
            validationErrors: {},
        };
    },
    props: ["id", "path"],
    mounted() {
        axios
            .get(this.path + "/" + this.id + "/detail")
            .then((response) => {
                this.loading = false;
                this.data = response.data;
            })
            .catch((e) => {
                this.loading = false;
                alert(e);
            });
    },
    methods: {
        save() {
            this.saving = true;
            if (this.id != 0) {
                axios
                    .patch(this.path + "/" + this.id, this.data)
                    .then((response) => {
                        window.location = this.path;
                    })
                    .catch((error) => {
                        this.handleError(error);
                    });
            } else {
                axios
                    .post(this.path, this.data)
                    .then((response) => {
                        window.location = this.path;
                    })
                    .catch((error) => {
                        this.handleError(error);
                    });
            }
        },
        handleError(error) {
            this.saving = false;
            if (error.response != undefined) {
                if (error.response.status == 422) {
                    this.validationErrors = error.response.data.errors;
                } else {
                    this.alertError(this, {text: error.response.data.message})
                }
            } else {
                console.log(error)
            }
        },
        errorClass(item) {
            return {
                "form-control": true,
                "is-invalid": this.validationErrors.hasOwnProperty(item),
            };
        },
    },
    components: {
        Selectize,
    },
};
</script>
