<div class="bg-slate-700 px-10 relative">
    <ul class="flex">
        <li><a class="block py-4 px-8 bg-amber-300 font-bold" href="/admin">Dashboard</a></li>
        <li class="mr-auto"><label for="user_info" class="select-none block py-4 cursor-pointer font-bold text-white px-3 hover:bg-white/10" href="#"><?= $teacher['full_name'] ?></label></li>
    </ul>
    <div class="absolute top-full left-10 p-2 bg-white shadow has-[:checked]:block hidden w-60">
        <input type="checkbox" id="user_info" class="hidden">
        <table class="mb-2 mx-auto text-center">
            <tbody>
                <tr>
                    <td class="px-4 py-3 border"><?= $teacher['username'] ?></td>
                </tr>
                <tr>
                    <td class="px-4 py-3 border"><?= $teacher['subject'] ?></td>
                </tr>
            </tbody>
        </table>
        <form action="/admin/logout.php" method="post" class="block mb-2 w-full">
            <input type="submit" value="Logout" name="logout" class="block text-red-600 hover:bg-gray-100 cursor-pointer px-3 py-2 w-full">
        </form>
        <a href="/admin/reset.php" class="text-center block text-red-600 hover:bg-gray-100 px-3 py-2 w-full">Reset DB</a>
    </div>
</div>