<template>
    <header>
        <img src="../../assets/imgs/logo.png">
        <nav>
            <ul>
                <li><a href="/register">新規登録</a></li>
                <li><a href="/login">ログイン</a></li>
            </ul>
        </nav>
    </header>
</template>

<style scoped>
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 10px;
}

header img {
    width: 200px;
    height: auto;
}

header nav ul li {
    list-style: none;
    display: inline-block;
    width: 100px
}

header nav ul li a {
    text-decoration: none;
    color: white;
}
</style>
