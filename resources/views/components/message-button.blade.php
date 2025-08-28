@props(['userId', 'buttonText' => 'Message', 'buttonClass' => 'px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors'])

<button onclick="startConversationWith({{ $userId }})" class="{{ $buttonClass }}">
    <i class="fa-solid fa-message mr-2"></i>{{ $buttonText }}
</button>

<script>
// Global function to start conversation with any user
async function startConversationWith(userId) {
    // Check if user is logged in
    if (!document.querySelector('meta[name="csrf-token"]') && !document.querySelector('input[name="_token"]')) {
        alert('{{ __('trans.please_login_to_start_conversation') }}');
        window.location.href = '/login';
        return;
    }
    
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
        
        if (!token) {
            alert('{{ __('trans.please_login_to_start_conversation') }}');
            window.location.href = '/login';
            return;
        }
        
        const response = await fetch('/chat/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                other_user_id: userId
            })
        });
        
        if (response.ok) {
            const result = await response.json();
            window.location.href = `/chat/${result.conversation_id}`;
        } else if (response.status === 401) {
            alert('{{ __('trans.please_login_to_start_conversation') }}');
            window.location.href = '/login';
        } else {
            const error = await response.json();
            alert(error.message || '{{ __('trans.failed_to_start_conversation') }}');
        }
    } catch (error) {
        console.error('Error starting conversation:', error);
        alert('{{ __('trans.failed_to_start_conversation') }}');
    }
}
</script>
