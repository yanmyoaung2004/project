<script setup>
import BaseInput from "@/Components/BaseInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { fromJSON } from "postcss";
import { ref, watch } from "vue";

const props = defineProps({
    warehouses: {
        type: Array,
    },
});

const form = useForm({
    product_id: 0,
    damage_qty: 0,
});

let errors = ref({});

watch(
    () => form.product_id,
    (productID, newProductID) => {
        if (productID && productID !== newProductID) {
            const product_id = props.warehouses.find((p) => p.id == productID);
            if (product_id) {
                errors.value.product_id = "";
            } else {
                errors.value.product_id = "Invalid Product ID.";
            }
        }
    }
);
watch(
    () => form.damage_qty,
    (oldDamage, newDamage) => {
        if (oldDamage !== newDamage) {
            const product = props.warehouses.find(
                (p) => p.id == form.product_id
            );
            if (product.availability > oldDamage) {
                errors.value.damage_qty = "";
            } else {
                errors.value.damage_qty = "Insufficient Amount";
            }
        }
    }
);

const addDamage = () => {
    setTimeout(() => {
        errors.value = {};
    }, 2000);

    if (errors.value.product_id) return;
    if (errors.value.damage_qty) return;

    form.post(route("warehouse.updateDamage"), {
        // need to update
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            setTimeout(() => {
                form.clearErrors();
            }, 2000);
            form.quantity = 0;
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="py-3">
        <form @submit.prevent="addDamage" class="space-y-4 max-w-4xl mx-auto">
            <h3 class="text-2xl font-bold text-center">Add Damage Product</h3>
            <div>
                <BaseInput
                    type="number"
                    label="Product ID"
                    v-model="form.product_id"
                    :error="errors.product_id"
                    class="w-full bg-gray-700 border-none p-2 rounded outline-none"
                />
            </div>
            <div>
                <BaseInput
                    type="number"
                    label="Quantity"
                    v-model="form.damage_qty"
                    :error="errors.damage_qty"
                    class="w-full bg-gray-700 border-none p-2 rounded outline-none"
                />
            </div>

            <div>
                <PrimaryButton>Create</PrimaryButton>
            </div>
        </form>
    </div>
</template>
