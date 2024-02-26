<template>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <NavLink
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            Dashboard
        </NavLink>
        <NavLink :href="route('admin.customers.index')" :active="route().current('admin.customers.*')"> Customers </NavLink>
    </div>
</template>

<script>
import NavLink from "@/Components/NavLink.vue";

export default { components: { NavLink } };
</script>

<style></style>
