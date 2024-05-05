<template>
    <div class="p-4 max-w-xl mx-auto  rounded-lg shadow">
        <h1 class="text-2xl font-semibold mb-4">Ask Agent</h1>
        <textarea v-model="message" rows="5" class="block w-full form-input form-control-bordered py-3 h-auto mb-4" placeholder="Enter your question here..." id="description-update-thread-assistant-training-textarea-field" dusk="description"></textarea>
        <button @click="sendMessage" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" dusk="create-button">
            <span class="hidden md:inline">Ask Agent</span>
            <span class="md:hidden">Create</span>
        </button>
    </div>
</template>

<script>

export default {
    props: ['resourceName', 'resourceId', 'panel'],
    data() {
        return {
            message: '',
        };
    },
    methods: {
        sendMessage() {
            Nova.post('/api/messages', { text: this.message })
                .then(response => {
                    // Handle success
                    console.log('Message sent successfully:', response);
                    this.message = ''; // Clear the textarea after sending
                })
                .catch(error => {
                    // Handle error
                    console.error('Error sending message:', error);
                });
        }
    }
}
</script>

<style>
/* Additional styles can be added here if needed */
</style>
