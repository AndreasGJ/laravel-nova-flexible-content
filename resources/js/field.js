Nova.booting((Vue, router, store) => {
    Vue.component('index-flexible-content', require('./components/IndexField'))
    Vue.component('detail-flexible-content', require('./components/DetailField'))
    Vue.component('form-flexible-content', require('./components/FormField'))
})
