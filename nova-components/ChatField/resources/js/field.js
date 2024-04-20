import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-chat-field', IndexField)
  app.component('detail-chat-field', DetailField)
  app.component('form-chat-field', FormField)
})
