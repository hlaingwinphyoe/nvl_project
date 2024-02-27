<template>
    <el-dialog
        :modelValue="show"
        @update:modelValue="show = $event"
        @close="closeDialog"
        @open="openDialog"
        draggable
        :title="dialogTitle"
        :close-on-click-modal="true"
    >
        <div class="mb-4">
            <div class="flex justify-between items-center">
                <h4 class="mb-2 ml-2">
                    Name :
                    <span class="font-bold text-black">{{
                        data.customer_name
                    }}</span>
                </h4>
                <h4 class="mb-2 ml-2">
                    Total Price :
                    <span class="font-bold text-black"
                        >{{ data.total_price }} Ks</span
                    >
                </h4>
            </div>
            <el-table :data="data.order_items" style="width: 100%">
                <el-table-column label="Serie">
                    <template #default="scope">
                        {{ scope.row.title }}
                    </template>
                </el-table-column>
                <el-table-column
                    prop="unit_price"
                    width="180"
                    label="Unit Price"
                />
            </el-table>
        </div>
    </el-dialog>
</template>

<script>
import { reactive, toRefs } from "vue";
export default {
    props: ["show", "title", "data", "series", "customers"],
    emits: ["closed"],
    setup(props, context) {
        const state = reactive({
            dialogTitle: "",
            doubleClick: false,
        });

        const closeDialog = () => {
            context.emit("closed");
        };

        const openDialog = () => {
            state.dialogTitle = props.title;
        };

        return {
            ...toRefs(state),
            openDialog,
            closeDialog,
        };
    },
};
</script>

<style></style>
