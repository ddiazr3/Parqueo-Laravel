<template>
    <div>
        <div class="row">
            <div class="col-sm-6 mb-2" v-if="inputSearch">
                <input type="text" class="form-control sm" placeholder="Buscar..." @keyup="buscar" v-model="textSearch">
            </div>
            <div class="col-sm-6" v-if="btnDowload">
                <export-excel
                    class   = "btn btn-success btn-sm"
                    :data   = "dataget"
                    :fields = "headerDowloadExcel"
                    worksheet = "Export"
                >
                    <i class="fa fa-file-excel"></i>
                </export-excel>
<!--                <button type="button" class="btn btn-sm btn-danger">-->
<!--                    <i class="fa fa-file-pdf"></i>-->
<!--                </button>-->

            </div>
        </div>

        <vuetable
            ref="vuetable"
            :api-mode="false"
            :fields="header"
            :per-page="perPage"
            :data-manager="dataManager"
            pagination-path="pagination"
            :show-sort-icons="true"
            :sortableIcon="true"
            :ascendingIcon="true"
            :css="css.table"
            @vuetable:pagination-data="onPaginationData"

        >
            <div slot="actions" slot-scope="props">
                <button v-for="button in buttons" :class="'btn '+button.class" @click="onActionClicked(button.action, props.rowData.id)" v-html="button.icon"></button>
            </div>
        </vuetable>
        <div style="padding-top:10px">
            <vuetable-pagination ref="pagination"
                                 :css="css.pagination"
                                 @vuetable-pagination:change-page="onChangePage"
            ></vuetable-pagination>
        </div>
    </div>
</template>
<script>
import Vuetable from 'vuetable-2'
import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
import CssForBootstrap4 from './disaingTable.js'
import _ from 'lodash'

export default {
    props: {
        header: {
            type: Array,
            default: []
        },
        body: {
            type: Array,
            default: []
        },
        buttons: {
            type: Array,
            default: []
        },
        inputSearch: {
            type: Boolean,
            default: true
        },
        btnDowload: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            field: this.header,
            perPage: 20,
            css: CssForBootstrap4,
            textSearch: '',
            dataget: this.body,
            pla: 'placa'
        }
    },
    components: {
        Vuetable,
        VuetablePagination
    },
    watch: {
        dataget(newVal, oldVal) {
            this.$refs.vuetable.reload();
        },
        body(){
          this.dataget = this.body
        },
    },
    computed: {
        headerDowloadExcel(){
            let he = new Object()
            this.header.map(el => {
                he[el.title] = el.name
            })
            return he
        }
    },
    methods: {
        onPaginationData(paginationData) {
            this.$refs.pagination.setPaginationData(paginationData);
        },
        onChangePage(page) {
            this.$refs.vuetable.changePage(page);
        },
        dataManager(sortOrder, pagination) {
            if (this.dataget.length < 1) return [];

            let local = this.dataget;

            // sortOrder can be empty, so we have to check for that as well
            if (sortOrder.length > 0) {
                console.log("orderBy:", sortOrder[0].sortField, sortOrder[0].direction);
                local = _.orderBy(
                    local,
                    sortOrder[0].sortField,
                    sortOrder[0].direction
                );
            }

            pagination = this.$refs.vuetable.makePagination(
                local.length,
                this.perPage
            );
            console.log('pagination:', pagination)
            let from = pagination.from - 1;
            let to = from + this.perPage;

            return {
                pagination: pagination,
                data: _.slice(local, from, to)
            };
        },
        onActionClicked(action, id) {
            this.$emit('action',action, id)
        },
        buscar(){
            if(this.textSearch.length < 1){
                this.dataget = this.body
                this.$refs.vuetable.refresh();
                return
            }
            this.$emit('buscar',this.textSearch)
        },

    }
}
</script>
<style>
.vuetable th.sortable:hover {
    color: red;
    cursor: pointer;
}
.btn {
    margin-right: 10px;
}
.active{
    color: white;
    background-color: #3490dc;
}
.disabled{
    color: white;
    background-color: darkgrey;
}
</style>
