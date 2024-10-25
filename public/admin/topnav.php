<div class="bg-slate-700 px-10 relative">
    <ul class="flex">
        <li><a class="block py-4 px-8 bg-amber-300 font-bold" href="/admin">Dashboard</a></li>
        <li class="ml-auto"><label for="user_info" class="select-none block py-4 cursor-pointer font-bold text-white px-3 hover:bg-white/10" href="#">@<?= $_SESSION['teacher'] ?></label></li>
    </ul>
    <div class="absolute top-full right-10 p-2 bg-white shadow has-[:checked]:block hidden w-60">
        <input type="checkbox" id="user_info" class="hidden">
        <form action="/admin/logout.php" method="post" class="block mb-2 w-full">
            <input type="submit" value="Logout" name="logout" class="block text-red-600 hover:bg-gray-100 cursor-pointer px-3 py-2 w-full">
        </form>
        <a href="/admin/reset.php" class="text-center block text-red-600 hover:bg-gray-100 px-3 py-2 w-full">Reset DB</a>
    </div>
</div>