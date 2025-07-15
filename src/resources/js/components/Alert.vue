<template>
    <div :class="style" role="alert">
        {{ title }}
        <hr>
        {{ details.message }}
        <br>
        <ul v-if="details.data">
            <li v-for="error, key in details.data" :key="key">{{ error[0] }}</li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ['type', 'title', 'details'],
        computed: {
            style(){
                return 'alert alert-'+this.type;
            }
        }
    }
</script>
