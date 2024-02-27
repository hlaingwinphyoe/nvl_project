<template>
    
</template>

<script>
import { onMounted, reactive, toRefs } from "vue";
import { router } from "@inertiajs/vue3";
export default {
    props: ["show", "title", "data"],
    emits: ["closed"],
    setup(props, context) {
        const state = reactive({
            dialogTitle: "",
            doubleClick: false,

            customer_id: "",
        });

        const closeDialog = () => {
            context.emit("closed");
        };

        const openDialog = () => {
            state.dialogTitle = props.title;
            state.customer_id = props.data.customer_id;

            if(state.customer_id)
            {
                getSeries();
            }
        };

        const getSeries = () => {
            router.get(
                route("admin.orders.getSeriesByCustomer", state.customer_id),
                {},
                {
                    onSuccess: (page) => {},
                }
            );
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
