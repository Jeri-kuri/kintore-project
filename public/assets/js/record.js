document.addEventListener("DOMContentLoaded", function() {
    //部位選択のボタン
    const buttons = document.querySelectorAll('.nav-button');
    const form = document.getElementById("training-form");
    const selectedTrainingInput = document.getElementById("selected-training");

    // データ属性から画像のURLを取得する
    const inputContainer = document.querySelector(".input-container");
    const deleteImgURL = inputContainer.dataset.deleteImg;
    const addImgURL = inputContainer.dataset.addImg;

    // 画像のHTMLを生成する
    const deleteImgHTML = `<img src="${deleteImgURL}" alt="x-mark" style="height: 40px; width: 40px;">`;
    const addImgHTML = `<img src="${addImgURL}" alt="add" style="height: 40px; width: 40px;">`;

    //体の部位の選択
    buttons.forEach(button =>{
        button.addEventListener('click',function(){
            //一つのボタンが選択された場合、active状態が外される
            if(this.classList.contains('active')){
                this.classList.remove('active');
            }else{
                //全てのボタンからactive状態が外される
                buttons.forEach(btn=> btn.classList.remove('active'));
                //クリックされたボタンがactive状態になる
                this.classList.add('active');
                //内容を隠しフィールドに保存する
                selectedTrainingInput.value = this.dataset.training;

                // フォームの中の部位をアプデする
                document.querySelectorAll('input[name^="trainings["][name$="[training_type]"]').forEach(input => {
                input.value = selectedTrainingType;
            });
               
            }
        });
    });

    //部位が選択されてるかどうかチェックする
    form.addEventListener('submit',function (event){
        if(!selectedTrainingInput.value){
            event.preventDefault();
            alert("Please select a training type");
        }
    });


    //トレーニングブロックを追加できるようにする
    let exerciseIndex = 1; // カウンター
    document.querySelector(".add-set").addEventListener("click",function(event){
        event.preventDefault(); //フォームの送信を防ぐ
        const selectedTrainingType = selectedTrainingInput.value;// 部位の情報を受け取る

        
        //新しくトレーニングのブロック作成
        const newTrainingBlock = document.createElement("div");
        newTrainingBlock.classList.add("training-block"); 
        newTrainingBlock.dataset.exerciseIndex = exerciseIndex;

        //種目とレップ数の入力フォームを作成
        newTrainingBlock.innerHTML =  `<input type="hidden" id="selected-training" name="trainings[${exerciseIndex}][training_type]" value="${selectedTrainingType}" >
        <div class="training-name-wrapper">
        <div class="training-name-container">
            <input type="text" name="trainings[${exerciseIndex}][name]" required>
        </div>
        <button type="button" class="deletion">
           ${deleteImgHTML}
        </button>
        </div>

    <div class="weight-rep-wrapper">
        <div class="training-details">
            <div class="sets">
                <span class="set-num">1</span>
                <input type="number" name="trainings_detail[${exerciseIndex}][sets][0][weight]" placeholder="重量" > kg
                <span class="separator">&nbsp;x&nbsp;</span>
                <input type="number" name="trainings_detail[${exerciseIndex}][sets][0][reps]" placeholder="レップ数" > reps
            </div>
        </div>
        <button type="button" class="add-rep">${addImgHTML}</button>
    </div>
    `;
        form.insertBefore(newTrainingBlock,document.querySelector(".add-set"));
        exerciseIndex++;
    });

    
    form.addEventListener("click", function(event){
        //削除ボタンがクリックされるとトレーニングブロック全体が削除される
        if(event.target.closest('.deletion')){
            event.preventDefault();
            event.target.closest(".training-block").remove();
        }

        //レップ追加ボタンがクリック
        if(event.target.closest(".add-rep")){
            event.preventDefault();//フォーム送信を防ぐ

            const addRepButton = event.target.closest(".add-rep");
            const wrapper = addRepButton.closest(".weight-rep-wrapper");


            const trainingDetails = wrapper.querySelector(".training-details");
            const setCount = trainingDetails.querySelectorAll(".sets").length; 
            const newSet = document.createElement("div");
            newSet.classList.add("sets");
            
            
            newSet.innerHTML = `
            <span class="set-num">${setCount + 1}</span>
            <input type="number" name="trainings_detail[${exerciseIndex -1}][sets][${setCount}][weight]" placeholder="重量" >
            <span class="separator">&nbsp;x&nbsp;</span>
            <input type="number" name="trainings_detail[${exerciseIndex -1}][sets][${setCount}][reps]" placeholder="レップ数" >
        `;
        //新しいセットを.training-detailsに追加する
        trainingDetails.appendChild(newSet);
        }
    });
   
});