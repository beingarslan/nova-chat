<template>
    <div class="chat-container">
        <div class="chat-messages" ref="messageContainer">
            <div v-for="group in groupedMessages" :key="group.date" class="message-group">
                <div class="date-divider">{{ group.date }}</div>
                <div v-for="message in group.messages" :key="message.id"
                    :class="message.from_id == fromID ? 'mine' : 'theirs'" class="message">
                    <div class="message-body">{{ message.body }}</div>
                    <div class="message-time">{{ formatTime(message.created_at) }}</div>
                </div>
            </div>
        </div>
        <div class="send-message">
            <input v-model="newMessage" placeholder="Type a message..." class="message-input" @keyup.enter="sendMessage" />
            <button @click="sendMessage" class="send-button">Send</button>
        </div>
    </div>
</template>


<script>
export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            messages: [],
            newMessage: '',
            fromID: null,
        };
    },

    mounted() {
        setInterval(() => {
            this.fetchMessages();
        }, 2000);
    },


    computed: {
        groupedMessages() {
            const grouped = this.messages.reduce((acc, message) => {
                // Extract just the date part from the created_at timestamp
                const date = message.created_at.split('T')[0];
                if (!acc[date]) {
                    acc[date] = [];
                }
                acc[date].push(message);
                return acc;
            }, {});

            // Convert the object back to an array for easier iteration in the template
            return Object.keys(grouped).map(date => ({ date, messages: grouped[date] }));
        },
    },


    created() {
        this.fetchMessages();
    },

    updated() {
        this.scrollToBottom();
    },

    methods: {
        async fetchMessages() {
            const response = await fetch(`/messages/${this.resourceId}`, {
                headers: {
                    'Accept': 'application/json',
                    // Add Authorization header if needed, for example:
                    // 'Authorization': 'Bearer ' + token,
                },
            });
            if (response.ok) {
                const data = await response.json();

                this.messages = data.messages;
                this.fromID = data.loggedUserId;
            } else {
                console.error('Failed to fetch messages:', response.statusText);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return; // Prevent sending empty messages

            const response = await fetch(`/messages/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    // Add CSRF token for Laravel applications
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    // Add Authorization header if needed
                },
                body: JSON.stringify({
                    body: this.newMessage,
                    to_id: this.resourceId, // The recipient's user ID
                }),
            });

            if (response.ok) {
                this.newMessage = ''; // Clear input after sending
                this.fetchMessages(); // Refresh messages to display the new one
            } else {
                console.error('Failed to send message:', response.statusText);
                // Optionally, display an error message to the user
            }
        },

        scrollToBottom() {
            this.$refs.messageContainer.scrollTop = this.$refs.messageContainer.scrollHeight;
        },

        formatTime(dateTime) {
            const date = new Date(dateTime);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
    },
}
</script>


<style scoped>
.chat-messages {
    flex-grow: 1;
    overflow-y: auto;
    /* Ensures scrolling within the message area */
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #e1e1e8;
    margin-bottom: 10px;
    max-height: 400px;
    /* Adjust based on your needs */
}

.message-group {
    margin-bottom: 20px;
}

.date-divider {
    text-align: center;
    margin-bottom: 10px;
    color: #888;
    font-size: 0.9em;
}

.chat-container {
    display: flex;
    flex-direction: column;
    height: 100%;
}


.message {
    display: flex;
    flex-direction: column;
    margin-bottom: 12px;
    align-items: flex-start;
}

.mine {
    align-items: flex-end;
}

.message-body {
    max-width: 60%;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    margin-bottom: 4px;
    /* Space between message body and time */
}

.mine .message-body {
    background-color: #007bff;
}

.theirs .message-body {
    background-color: #28a745;
}

.message-time {
    font-size: 0.8em;
    color: #666;
}

.send-message {
    display: flex;
}

.message-input {
    flex-grow: 1;
    margin-right: 10px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 20px;
}

.send-button {
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
}

.send-button:hover {
    background-color: #0056b3;
}
</style>
