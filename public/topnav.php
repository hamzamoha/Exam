<div class="bg-indigo-700 px-10 relative">
    <ul class="flex">
        <li><a class="block py-4 px-8 bg-yellow-400 font-bold" href="/">Accueil</a></li>
        <li class="ml-auto"><label for="user_info" class="select-none block py-4 cursor-pointer font-bold text-white px-3 hover:bg-white/10"><?= $student['first_name'] . " " . $student['last_name'] ?></label></li>
    </ul>
    <div class="absolute top-full right-10 p-3 rounded-lg bg-white shadow has-[:checked]:block hidden">
        <input type="checkbox" id="user_info" class="hidden">
        <h3 class="text-center py-3 text-2xl"><?= $student['first_name'] . " " . $student['last_name'] ?></h3>
        <table class="mb-2">
            <tbody>
                <tr>
                    <th class="px-4 py-3 border">Code Massar</th>
                    <td class="px-4 py-3 border"><?= $student['student_number'] ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-3 border">Date de Naissance</th>
                    <td class="px-4 py-3 border"><?= date("d M Y", strtotime($student['date_of_birth'])) ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-3 border">Classe</th>
                    <td class="px-4 py-3 border">1APIC-5</td>
                </tr>
            </tbody>
        </table>
        <form action="/logout.php" method="post" class="block w-full">
            <input type="submit" value="Logout" name="logout" class="block text-red-600 hover:bg-gray-100 cursor-pointer px-3 py-2 w-full">
        </form>
    </div>
</div>