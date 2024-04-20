<template>
    <div>
      <ul v-for="message in messages" :key="message.id">
        <li>{{ message.body }}</li>
      </ul>
      <input v-model="newMessage" @keyup.enter="sendMessage">
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        messages: [],
        newMessage: ''
      };
    },
    methods: {
      sendMessage() {
        // Add logic to send message to the server
        this.newMessage = '';
      }
    },
    mounted() {
      // Logic to load messages and listen for new ones
    }
  }
  </script>
