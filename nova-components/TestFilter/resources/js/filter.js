import Filter from './components/Filter'

Nova.booting((app, store) => {
  app.component('test-filter', Filter)
})
