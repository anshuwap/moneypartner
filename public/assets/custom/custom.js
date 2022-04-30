//lead filter and reloade page
// $(document).ready(function () {
//     $("a#clear").click(function (e) {
//         e.preventDefault();
//         let url = $("form#filter-form").attr("action");
//         $("form#filter-form")
//             .find(
//                 "input[type=text],input[type=email],input[type=password],input[type=number],input[type=date],textarea,select,checkbox,radio"
//             )
//             .val("");
//         window.location.href = url;
//     });
// });

function intToEnglish(number) {
    var NS = [
        { value: 10000000, str: "Crore" },
        { value: 100000, str: "Lakh" },
        { value: 1000, str: "Thousand" },
        { value: 100, str: "Hundred" },
        { value: 90, str: "Ninety" },
        { value: 80, str: "Eighty" },
        { value: 70, str: "Seventy" },
        { value: 60, str: "Sixty" },
        { value: 50, str: "Fifty" },
        { value: 40, str: "Forty" },
        { value: 30, str: "Thirty" },
        { value: 20, str: "Twenty" },
        { value: 19, str: "Nineteen" },
        { value: 18, str: "Eighteen" },
        { value: 17, str: "Seventeen" },
        { value: 16, str: "Sixteen" },
        { value: 15, str: "Fifteen" },
        { value: 14, str: "Fourteen" },
        { value: 13, str: "Thirteen" },
        { value: 12, str: "Twelve" },
        { value: 11, str: "Eleven" },
        { value: 10, str: "Ten" },
        { value: 9, str: "Nine" },
        { value: 8, str: "Eight" },
        { value: 7, str: "Seven" },
        { value: 6, str: "Six" },
        { value: 5, str: "Five" },
        { value: 4, str: "Four" },
        { value: 3, str: "Three" },
        { value: 2, str: "Two" },
        { value: 1, str: "One" },
    ];

    var result = "";
    for (var n of NS) {
        if (number >= n.value) {
            if (number <= 99) {
                result += n.str;
                number -= n.value;
                if (number > 0) result += " ";
            } else {
                var t = Math.floor(number / n.value);
                // console.log(t);
                var d = number % n.value;
                if (d > 0) {
                    return (
                        intToEnglish(t) + " " + n.str + " " + intToEnglish(d)
                    );
                } else {
                    return intToEnglish(t) + " " + n.str;
                }
            }
        }
    }
    return result;
}

function numberToWord(number) {
    return  intToEnglish(number);
}

// console.log(intToEnglish(99))
// console.log(intToEnglish(991199))
// console.log(intToEnglish(123456799))
