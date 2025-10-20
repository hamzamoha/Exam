<div class="bg-gradient-to-r from-[#5c61a3] to-[#105ba6] px-10 relative">
    <ul class="flex">
        <li><a class="block py-4 px-8 bg-[#ff8723] font-bold text-white" href="/">صفحة البداية</a></li>
        <li class="mr-auto"><label for="user_info" class="select-none block py-4 cursor-pointer font-bold text-white px-3 hover:bg-white/10"><?= $student['first_name'] . " " . $student['last_name'] ?></label></li>
    </ul>
    <div class="absolute top-full left-10 p-3 rounded-lg bg-white shadow has-[:checked]:block hidden">
        <input type="checkbox" id="user_info" class="hidden">
        <h3 class="text-center py-3 text-2xl"><?= $student['first_name'] . " " . $student['last_name'] ?></h3>
        <table class="mb-2">
            <tbody>
                <tr>
                    <th class="px-4 py-3 border">رمز مسار</th>
                    <td class="px-4 py-3 border"><?= $student['student_number'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-3 border">تاريخ الازدياد</th>
                    <td class="px-4 py-3 border"><?= date("d M Y", strtotime($student['date_of_birth'])) ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-3 border">القسم</th>
                    <td class="px-4 py-3 border"><?= $student['class'] ?></td>
                </tr>
            </tbody>
        </table>
        <a href="/change-password.php" class="block text-center hover:bg-gray-100 px-3 py-2 mb-2">تغيير كلمة السر</a>
        <form action="/logout.php" method="post" class="block w-full">
            <input type="submit" value="تسجيل الخروج" name="logout" class="block text-red-600 hover:bg-gray-100 cursor-pointer px-3 py-2 w-full">
        </form>
    </div>
</div>