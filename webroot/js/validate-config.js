 function validation() {
        
    $.validator.addMethod(
        // ルール名の設定
        "time",
        function(value,element) {
            return this.optional(element) || ([0-9]|2[0-3])(":"[0-9]|5[0-9]).test(value);
        },
        // エラーメッセージの設定
        "「時間:分」の形式で入力してください"
    )

    for($i=1; $i<=getsumatsu; $i++) {
        shukkin = "#intime-"+$i;
        taikin = "#outtime-"+$i;
        biko = "#bikou-"+$i;

        $("#editform").validate({
            rules: {
                shukkin: {
                    time : true,
                },
                taikin: {
                    time : true,
                },
                biko: {
                    maxlength: 7,
                },
            },
            messages: {
                shukkin: {
                    time : "「時間:分」の形式で入力してください"
                },
                taikin: {
                    time : "「時間:分」の形式で入力してください"
                },
                biko: {
                    maxlength: "7文字以内で入力してください",
                },
            },
            // バリデーションエラーの際に使うCSSを指定
            // errorClass: "validation-error",
            // errorElement: "span",
            // errorPlacement: function(error, element) {
            //     error.appendTo(element.data("error_place"));
            // },
        });
    }
}