<template>
    <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
        <NavLink
            :href="route('admin.dashboard')"
            :active="route().current('admin.dashboard')"
        >
            Dashboard
        </NavLink>
        <NavLink
            :href="route('admin.customers.index')"
            :active="route().current('admin.customers.*')"
        >
            Customers
        </NavLink>
        <NavLink
            :href="route('admin.series.index')"
            :active="route().current('admin.series.*')"
        >
            Series
        </NavLink>
        <NavLink
            :href="route('admin.orders.index')"
            :active="route().current('admin.orders.*')"
        >
            Orders
        </NavLink>
    </div>
</template>

<script>
import NavLink from "@/Components/NavLink.vue";

export default { components: { NavLink } };
</script>

<style></style>
