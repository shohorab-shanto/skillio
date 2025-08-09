<section class="max-w-[1200px] mx-auto rounded-2xl p-6 bg-white ">
    <div class="max-w-[1200px] mx-auto  rounded-2xl p-6 md:p-10 grid md:grid-cols-3 gap-6 items-center">
        <!-- Left: Image + Details -->
        <div class="flex flex-col md:flex-row items-center gap-6">
            <!-- Profile Image -->
            <div class="w-[150px] h-[150px] rounded-full overflow-hidden border-2 border-blue-500">
                <img src="{{ asset('assets/images/mentor.jpg') }}" alt="Profile" class="w-full h-full object-cover" />
            </div>

            <!-- Text Info -->
            <div class="text-center md:text-left space-y-1">
                <h2 class="text-xl font-semibold">Marina Amer</h2>
                <div class="flex items-center justify-center md:justify-start gap-1 text-sm text-gray-600">
                    <span class="text-yellow-500">★</span>
                    <span>4.5</span>
                    <span class="text-gray-400">(500 Reviews)</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Role</p>
                    <p class="font-semibold">Designer</p>
                </div>
            </div>
        </div>
        <!-- Bio/Experience (center of right side) -->
        <div class="flex flex-col  justify-center h-full text-center mt-10 md:mt-0">
            <p class="text-sm text-gray-500">Bio/Work Experience</p>
            <p class="font-semibold">8 Years of Experience</p>
        </div>
        <!-- Right: Online + Bio centered properly -->
        <div class="relative h-full">
            <!-- Online badge (top-right) -->
            <div class="absolute top-0 right-36 flex items-center gap-2 bg-gray-100 md:px-3 py-1 rounded-full text-sm">
                <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                <span class="text-gray-700">Online</span>
            </div>


        </div>
    </div>
    <!-- Categories -->
    <div class="flex flex-col lg:flex-row flex-wrap lg:items-center gap-4 justify-center lg:justify-start">
        <div class="flex flex-wrap gap-3 justify-center lg:justify-start flex-1">
            <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap">Content Creation</span>
            <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap">Content Creation</span>
            <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap">Content Creation</span>
            <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap">Content Creation</span>
        </div>

        <div class="flex gap-3">
            <button onclick="startConversationWithMentor({{ $mentor->user_id ?? 1 }})" 
                    class="px-4 py-2 border border-purple-700 text-purple-700 hover:bg-purple-700 hover:text-white rounded transition-colors duration-300 text-center whitespace-nowrap">
                <i class="fa-solid fa-message mr-2"></i>Message
            </button>
            <a href="#"
                class="block px-4 py-2 border hover:border-purple-700 hover:bg-base-100 hover:text-purple-700 rounded bg-purple-700 text-white transition-colors duration-300 text-center whitespace-nowrap">
                Review
            </a>
        </div>
    </div>






</section>

<script>
async function startConversationWithMentor(mentorUserId) {
    // Check if user is logged in
    if (!document.querySelector('meta[name="csrf-token"]') && !document.querySelector('input[name="_token"]')) {
        alert('Please log in to start a conversation.');
        window.location.href = '/login';
        return;
    }
    
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
        
        if (!token) {
            alert('Please log in to start a conversation.');
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
                other_user_id: mentorUserId
            })
        });
        
        if (response.ok) {
            const result = await response.json();
            window.location.href = `/chat/${result.conversation_id}`;
        } else if (response.status === 401) {
            alert('Please log in to start a conversation.');
            window.location.href = '/login';
        } else {
            const error = await response.json();
            alert(error.message || 'Failed to start conversation. Please try again.');
        }
    } catch (error) {
        console.error('Error starting conversation:', error);
        alert('Failed to start conversation. Please try again.');
    }
}
</script>
