<script setup>
import TableLayout from "@/Layouts/TableLayout.vue";
import UrgentOrderModal from "@/Components/Modals/UrgentOrderModal.vue";
import { filteredById } from "@/composable/search";
import { ref, computed } from "vue";
import { formatPrice } from "@/Components/customer/helper";

const props = defineProps({
    urgents: {
        type: Array,
    },
});

const headers = ref([
    "Id",
    "Product Names",
    "Quantity",
    "Truck Number",
    "Truck Capacity",
    "Pickup Date",
    "Total Price",
    "Status",
    "Details",
]);
const urgentObj = ref(null);
const urgentConfrimation = ref(false);
const search = ref("");

const filteredUrgentOrders = computed(() => {
    return filteredById(search.value, props.urgents);
});

const showUrgentOrderModal = (urgent) => {
    urgentConfrimation.value = true;
    urgentObj.value = urgent;
};
</script>

<template>
    <div v-if="urgents.length > 0">
        <div class="flex justify-between items-center">
            <Search
                @searching="(val) => (search = val)"
                :howToSearch="'Id'"
                class="w-3/4"
            />
            <Sorting
                :items="filteredUrgentOrders"
                sort-by="id"
                @sorted="(val) => (urgents = val)"
                class="w-[370px]"
            />
        </div>
        <div
            class="sm:rounded-lg"
            :class="{ 'overflow-x-scroll': filteredUrgentOrders.length > 0 }"
        >
            <TableLayout
                :headers="headers"
                :is-admin="$page.props.auth.user.isAdmin"
                :is-department="$page.props.auth.user.department === 'LOGISTIC'"
                v-if="filteredUrgentOrders.length > 0"
            >
                <template #tbody>
                    <tr
                        class="border-b item"
                        v-for="(urgent, index) in urgents"
                        :key="urgent.id"
                    >
                        <td class="py-4 px-2 text-center">{{ index + 1 }}</td>
                        <td class="py-4 px-2 text-center">{{ urgent.id }}</td>
                        <td class="py-4 text-center" style="width: 300px">
                            <span
                                v-for="product in urgent.products"
                                :key="product.id"
                                >{{
                                    product.name.length > 15
                                        ? product.name.slice(0, 10) + "..."
                                        : product.name
                                }},</span
                            >
                        </td>
                        <td class="py-4 px-2 text-center">
                            {{ urgent.order_quantity }}
                        </td>
                        <td class="py-4 px-2 text-center">
                            {{ urgent.truck_number }}
                        </td>
                        <td class="py-4 px-2 text-center">
                            {{ urgent.capacity }}
                        </td>
                        <td class="py-4 px-2 text-center">{{ urgent.date }}</td>
                        <td class="py-4 px-2 text-center">
                            {{ formatPrice(urgent.total_price) }}$
                        </td>
                        <td
                            class="py-4 px-2 text-center"
                            :class="{
                                'text-blue-400': urgent.status === 'order',
                                'text-green-500': urgent.status === 'deliver',
                                'text-red-500': urgent.status === 'cancel',
                            }"
                        >
                            {{ urgent.status }}
                        </td>
                        <td class="py-4 px-2 text-center">
                            <button
                                class="text-blue-500 hover:text-blue-600 hover:underline duration-200 font-semibold"
                                @click="showUrgentOrderModal(urgent)"
                            >
                                See Details
                            </button>
                        </td>
                    </tr></template
                >
            </TableLayout>
            <template v-else>
                <NoResults />
            </template>
        </div>
    </div>
    <div v-else>
        <p>Don't have any urgent orders!</p>
    </div>
    <UrgentOrderModal
        :urgent="urgentObj"
        :urgentConfrimation="urgentConfrimation"
        @hide-modal="urgentConfrimation = false"
    />
</template>
