<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login(); 
$user_id = $_SESSION['user_id'];


$conv_sql = "
    SELECT DISTINCT 
        u.id, 
        u.username, 
        u.profile_image 
    FROM messages m 
    JOIN user u ON (m.sender_id = u.id OR m.receiver_id = u.id)
    WHERE (m.sender_id = $user_id OR m.receiver_id = $user_id) AND u.id != $user_id
";
$conversations = $conn->query($conv_sql);


$chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$chat_user = null;
$messages = null;

if ($chat_user_id) {
    
    $u_stmt = $conn->prepare("SELECT id, username, profile_image, phone FROM user WHERE id = ?");
    $u_stmt->bind_param("i", $chat_user_id);
    $u_stmt->execute();
    $chat_user = $u_stmt->get_result()->fetch_assoc();

    if ($chat_user) {
        $m_stmt = $conn->prepare("
            SELECT * FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY created_at ASC
        ");
        $m_stmt->bind_param("iiii", $user_id, $chat_user_id, $chat_user_id, $user_id);
        $m_stmt->execute();
        $messages = $m_stmt->get_result();
    }
}

require 'layout_header.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 h-[calc(100vh-140px)]"> <!-- Fixed height calc -->
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex h-full">
        
        <!-- Sidebar (User List) -->
        <div class="w-1/3 border-r border-gray-200 bg-gray-50 flex flex-col">
            <div class="p-4 border-b border-gray-200">
                <h2 class="font-bold text-gray-800 text-lg">Messages</h2>
            </div>
            
            <div class="flex-grow overflow-y-auto">
                <?php if ($conversations->num_rows > 0): ?>
                    <?php while($c = $conversations->fetch_assoc()): ?>
                        <a href="messages_inbox.php?user_id=<?php echo $c['id']; ?>" class="flex items-center gap-3 p-4 hover:bg-white border-b border-gray-100 transition <?php echo ($chat_user_id == $c['id']) ? 'bg-white border-l-4 border-l-primary-500' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($c['profile_image'] ?? 'assets/default_user.png'); ?>" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm"><?php echo htmlspecialchars($c['username']); ?></h4>
                                <p class="text-xs text-gray-500">Click to chat</p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-8 text-center text-gray-500 text-sm">No conversations yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="w-2/3 flex flex-col bg-white">
            <?php if ($chat_user): ?>
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <img src="<?php echo htmlspecialchars($chat_user['profile_image'] ?? 'assets/default_user.png'); ?>" class="w-8 h-8 rounded-full object-cover">
                        <span class="font-bold text-gray-900"><?php echo htmlspecialchars($chat_user['username']); ?></span>
                    </div>

                    <?php if (!empty($chat_user['phone'])): ?>
                        <a href="tel:<?php echo htmlspecialchars($chat_user['phone']); ?>" class="flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-full font-bold hover:bg-green-600 transition shadow-sm text-sm">
                            <i class='bx bxs-phone'></i> Call
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Messages -->
                <div class="flex-grow overflow-y-auto p-6 space-y-4 bg-white" id="messageBox">
                    <?php if ($messages->num_rows > 0): ?>
                        <?php while($msg = $messages->fetch_assoc()): ?>
                            <div class="flex <?php echo ($msg['sender_id'] == $user_id) ? 'justify-end' : 'justify-start'; ?>">
                                <div class="max-w-[70%] px-4 py-2 rounded-2xl text-sm <?php echo ($msg['sender_id'] == $user_id) ? 'bg-primary-500 text-white rounded-br-none' : 'bg-gray-100 text-gray-800 rounded-bl-none'; ?>">
                                    <?php echo htmlspecialchars($msg['message']); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-gray-400 mt-10">
                            <i class='bx bx-message-dots text-4xl mb-2'></i>
                            <p>Say hello to start the conversation!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Input -->
                <div class="p-4 border-t border-gray-200 bg-gray-50">
                    <form action="messages_send.php" method="POST" class="flex gap-2">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="receiver_id" value="<?php echo $chat_user_id; ?>">
                        <input type="text" name="message" placeholder="Type a message..." required autocomplete="off" class="flex-grow px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none">
                        <button type="submit" class="bg-primary-500 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-primary-600 transition shadow-sm">
                            <i class='bx bxs-send'></i>
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <div class="flex-grow flex flex-col items-center justify-center text-gray-400">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <i class='bx bx-chat text-4xl text-gray-300'></i>
                    </div>
                    <p>Select a conversation to start chatting.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    var messageBox = document.getElementById("messageBox");
    if(messageBox) {
        messageBox.scrollTop = messageBox.scrollHeight;
    }
</script>

<?php require 'layout_footer.php'; ?>