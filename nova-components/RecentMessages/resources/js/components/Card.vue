<template>
    <card class="flex flex-col divide-y dark:divide-gray-700">
        <div v-for="message in messages" :key="message.id"
             class="group p-4 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900 cursor-pointer">
            <div  class="flex justify-between items-center mb-1">
                <span class="font-semibold text-gray-900 dark:text-gray-200">{{ message.username }}</span>
                <a @click="navigateToThread(message.thread.id)" class="text-xs text-gray-500 dark:text-gray-400">{{ message.thread.name }}</a>
            </div>
            <div  class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ message.created_at }}</div>
            <div class="w-full flex min-h-8 px-1 py-1 rounded text-left text-gray-500 dark:text-gray-500 focus:outline-none focus:ring focus:ring-primary-200 dark:focus:ring-gray-600 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-800">
                {{ message.content }}
            </div>
            <div class="mt-2 transition-opacity duration-300 ease-in-out flex">
                <input type="text" v-model="message.reply" placeholder="Reply..."
                       class="form-input flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-700 focus:ring-opacity-50 dark:text-gray-50">
                <button @click="sendReply(message)"
                        class="ml-2 bg-blue-500 hover:bg-blue-600 dark:hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg focus:outline-none">
                    Send
                </button>
            </div>
        </div>
    </card>
</template>

<script>
export default {
    props: ['card'],
    computed: {
        messages() {
            return this.card.messages || [];
        }
    },
    methods: {
        sendReply(message) {
            let index = this.messages.findIndex(m => m.id === message.id);
            if (index !== -1) {
                // Remove the message from the list
                this.messages.splice(index, 1);
            }
            let reply = message.reply;
            message.reply = ''; // Clear input immediately to prepare for next input
            this.messages = this.messages.filter(item => {
                console.log({item: item, message: message});
                return item.id !== message.id;
            });
            console.log(this.messages.length);
            // Send the reply to the server
            Nova.request().post(`/nova-vendor/recent-messages/create-message/${message.thread.id}`, { reply })
                .then(response => {
                    // Handle success response
                    // Optimistically updating the UI
                })
                .catch(error => {
                    console.error("Error sending reply: ", error);
                    // Handle failure by adding the message back to the list
                    if (confirm('Failed to send reply. Would you like to retry?')) {
                        this.messages.push(message);
                    }
                });
        },
        navigateToThread(threadId) {
            window.location.href = "/resources/threads/" + threadId;
        }
    },
}
</script>
