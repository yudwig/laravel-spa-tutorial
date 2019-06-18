<template>
    <nav class="navbar">
        <!-- aタグの代わりに使うコンポーネント。(View Routerが提供)
        historyモードとhashモードの両方でコードを共通化する。
        (historyモードの場合、ページがリロードされないようにクリックイベントに割り込んでくれる。)
        -->
        <RouterLink class="navbar__brand" to="/">
            Vuesplash
        </RouterLink>
        <div class="navbar__menu">
            <div class="navbar__item">
                <button class="button">
                    <i class="icon ion-md-add"></i>
                    Submmit a photo
                </button>
            </div>
            <span class="navbar__item">
                username
            </span>
            <div class="navbar__item">
                <RouterLink class="button button--link" to="/login">
                    Login / Register
                </RouterLink>
            </div>
        </div>
    </nav>
</template>

