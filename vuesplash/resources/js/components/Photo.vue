<template>
    <div class="photo">
        <figure class="photo__wrapper">
            <img
                class="photo__image photo__image--portrait"
                :src="item.url"
                :alt="`Photo by ${item.owner.name}`"
                >
        </figure>
        <RouterLink
            class="photo__overlay"
            :to="`/photos/${item.id}`"
            :title="`View the photo by ${item.owner.name}`"
            >
            <div class="photo__controls">
                <button
                    class="photo__action photo__action--like"
                    title="Like Photo"
                    >
                    <i class="icon ion-md-heart"></i>12
                </button>
                <!-- クリック時詳細画面に遷移しないよう@stop -->
                <a
                    class="photo__action"
                    title="Download photo"
                    @click.stop
                    :href="`/photos/${item.id}/download`"
                    >
                    <li class="icon ion-md-arrow-round-down"></li>
                </a>
            </div>
            <div class="photo__username">
                {{item.owner.name}}
            </div>
        </RouterLink>
    </div>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true
            }
        }
    }
</script>
