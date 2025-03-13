
document.addEventListener("DOMContentLoaded",(event) =>{

    const currentDate = document.querySelector(".current-date");
    const selectedDate = document.querySelector(".log-data");
    const daysTag = document.querySelector(".days");
    const prevNextIcon = document.querySelectorAll(".arrow-left , .arrow-right");
    const logContainer = document.querySelector(".scrollable-container");

    let formattedDate = "";
    
    
    //新しい日付
    let date = new Date();
    let currYear = date.getFullYear();
    let currMonth = date.getMonth();
    
    //月の配列
    const months = ["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"];
    
    //カレンダの作成
    const renderCalendar = () => {
        const firstDayofMonth = new Date(currYear, currMonth, 1).getDay(); //月の最初の日
        const lastDateofMonth = new Date(currYear, currMonth +1, 0).getDate(); //月の最後の日付
        const lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(); //月の最後の日
        const lastDateofPrevMonth = new Date(currYear, currMonth, 0).getDate(); //前の月の最後の日付
        let liTag = "";
    
        //前の月の最後の日を加える
        for (let i = firstDayofMonth; i > 0; i--) {
            liTag += `<li class="inactive">${lastDateofPrevMonth -i + 1 }</li>`;
            
        }
    
        //月の日を加える
        for (let i = 1; i<= lastDateofMonth; i ++){
            //現在の日付と紐つけて、li にactive classを付け加える
            let isToday = i === date.getDate() && currMonth === new Date().getMonth()
                            && currYear === new Date().getFullYear()?"active" : "";
            liTag += `<li class="${isToday}">${i}</li>`;
        }
    
        //次の月の日を加える
        for(let i = lastDayofMonth; i < 6; i++){
            liTag += `<li class="inactive">${i - lastDayofMonth + 1 }</li>`;
        }
    
        currentDate.innerText = `${months[currMonth]} ${currYear}`;
        daysTag.innerHTML = liTag;
    
        addClickEventToDays();
    }
    
    
    //日を選択できるようにする
    const addClickEventToDays = () => {
        document.querySelectorAll(".days li").forEach(day => {
            day.addEventListener("click", () => {
                if (!day.classList.contains("inactive")) {
                    let selectedDay = day.innerText.padStart(2, '0'); 
                    let formattedDate = `${new Date().getFullYear()}-${(currMonth + 1).toString().padStart(2, '0')}-${selectedDay}`;
                

                    document.getElementById("date-log").innerText = `${months[currMonth]}${selectedDay}日`;
                    fetchTrainingData(formattedDate);//選択日で情報を取得
                    console.log(formattedDate);
                }
            });
        });
    };
    
    //POSTリクエストを送信する
    const fetchTrainingData = (selectedDate) => {
        console.log("Sending request to /category/exercise with date:", selectedDate);
    fetch("category/exercise", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `date=${encodeURIComponent(selectedDate)}` 
    })
    .then(response => {
        console.log("Response status:", response.status);  
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("Data received:", data);
        

        if (data.length > 0) {
            document.getElementById("training-log").innerText = `${data[0].category}トレ`;
            displayTrainingLog(selectedDate,data);
            displayEditLog(selectedDate,data);
        } else {
            document.getElementById("training-log").innerText = "データがありません";
            displayTrainingLog(selectedDate,data);
        }
    })
    .catch(error => console.error("Error fetching training data:", error));
};

    //トレーニングをログに表示させる
    const displayTrainingLog = (selectedDate, data) => {
        const menuWrapper = document.querySelector(".menu-wrapper");

        //初期化する
        menuWrapper.innerHTML = "";
        let logHTML = "";
        
        if (!selectedDate && data.length === 0) {
            let exerciseHTML = `
                <h3 id="exercise-name"></h3>
                <div class="rep-wrapper"></div>
                `;
            logHTML += `<p>データがありません</p>`;
            menuWrapper.innerHTML += exerciseHTML;

            return;
        }else{
             //同じ名前をグループ化する
            const groupedData = data.reduce((acc,item) => {
                if(!acc[item.name]){
                    acc[item.name] = [];
                }
                acc[item.name].push(item);
                return acc;
              },{});

        //グループごとにHTMLを生成する
        Object.keys(groupedData).forEach(name => {
             let exerciseHTML = `
                <h3 id="exercise-name" data-bind="text: exercise">${name}</h3>
                <div class="rep-wrapper">
                `;
            //セットを追加できるようにする
            groupedData[name].forEach(set => {
                exerciseHTML += `
               <div class="set">
                    <span class="weight">${set.weight}kg</span> 
                    <span class="separator">&nbsp;x&nbsp;</span>
                    <span class="reps">${set.reps}回</span>
                </div> `;

               
            });
            
            exerciseHTML += `</div>`;//rep-wrapperをとじる
            menuWrapper.innerHTML += exerciseHTML;
            })
        }
       
    };
    
    addClickEventToDays();
    renderCalendar();

    //編集ボタン
    const displayEditLog = (selectedDate,data) => {
        const editButton = document.querySelector(".log_edit");
        editButton.addEventListener("click", function(){
            const menuWrapper = document.querySelector(".menu-wrapper");
    
            //初期化する
            menuWrapper.innerHTML = "";
            let logHTML = "";
            
            if (!selectedDate && data.length === 0) {
                let exerciseHTML = `
                    <h3 id="exercise-name"></h3>
                    <div class="rep-wrapper"></div>
                    `;
                logHTML += `<p>データがありません</p>`;
                menuWrapper.innerHTML += exerciseHTML;
    
                return;
            }else{
                 //同じ名前をグループ化する
                const groupedData = data.reduce((acc,item) => {
                    if(!acc[item.name]){
                        acc[item.name] = [];
                    }
                    acc[item.name].push(item);
                    return acc;
                  },{});
    
            //グループごとにHTMLを生成する
            Object.keys(groupedData).forEach(name => {
                 let exerciseHTML = `
                    <input type="text" id="exercise-name" class="edit-exercise" value= ${name} />
                    
                    <div class="rep-wrapper">
                    `;
                //セットを追加できるようにする
                groupedData[name].forEach(set => {
                    exerciseHTML += `
                   <div class="set">
                         <input type="number" class ="edit-weight" value="${set.weight}" /> kg
                        <span class="separator">&nbsp;x &nbsp;</span>
                        <input type="number" class ="edit-rep"  value="${set.reps}"  />回
                    </div> `;
    
                   
                });
                
                exerciseHTML += `</div>`;//rep-wrapperをとじる
                menuWrapper.innerHTML += exerciseHTML;
                })
            }

            document.querySelector(".log_cancel").addEventListener("click", function() {
                displayTrainingLog(selectedDate, data); 
            });

            document.querySelector(".log_save").addEventListener("click", function () {
                saveEditedData(selectedDate);
            });
    

        });
  
        

    };

    const saveEditedData = (selectedDate) => {
        const exercises = [];
        
        document.querySelectorAll(".menu-wrapper").forEach((exerciseGroup, exerciseIndex) => {
            const exerciseName = exerciseGroup.querySelector("#exercise-name").value.trim();
            const sets = [];
    
            exerciseGroup.querySelectorAll(".set").forEach((setDiv, setIndex) => {
                const weight = setDiv.querySelector("#edit-weight").value;
                const reps = setDiv.querySelector("#edit-reps").value;

    
                if (weight && reps) {
                    sets.push({ weight, reps });
                }
            });
    
            if (exerciseName && sets.length > 0) {
                exercises.push({ 
                    [`trainings[${exerciseIndex}]][training_type]`]: "custom",
                    [`trainings[${exerciseIndex}]][sets]`]: exerciseName,
                    [`trainings_detail[${exerciseIndex}][sets]`]:sets.map((set, setIndex) => ({
                        [`trainings_detail[${exerciseIndex}][sets][${setIndex}][weight]`]: set.weight,
                        [`trainings_detail[${exerciseIndex}][sets][${setIndex}][reps]`]: set.reps
                    }))
                });
            }
        });
    
        // データをサーバーへ送信
        fetch("training/edit", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ date: selectedDate, exercises: exercises })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Server Response:", data);
            if (data.status === "success") {
                alert("更新しました！");
                fetchTrainingData(selectedDate); // 更新後のデータを再取得
            } else {
                console.log("Data to be sent:", JSON.stringify({ date: selectedDate, exercises }, null, 2));
                alert("更新に失敗しました：" + data.message);
            }
        })
        .catch(error => console.error("Error updating training data:", error));
    };
    
    


    // 記録のある日を数える
    const updateMonthlyCount = () =>{
        const year = currYear;
        const month = (currMonth + 1).toString().padStart(2, '0');

        fetch("counter/counter", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `year=${year}&month=${month}`
        })
        .then(response => {
            if(!response.ok){
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();

        })
           
        .then(data => {
            const uniqueDates = new Set(data.entries.map(entry => entry.date));

            console.log("Monthly count:", data.count);
            document.querySelector(".counter").innerText = `${data.count}回`;

            //記録のある日をカレンダにハイライト
            document.querySelectorAll(".days li").forEach(day => {
                let dayNumber = day.innerText.padStart(2, '0');
                let formattedDate = `${currYear}-${month}-${dayNumber}`;

                if(day.classList.contains('inactive')){
                    return;
                }

                if (uniqueDates.has(formattedDate)){
                    day.classList.add("has-record");
                }else{
                    day.classList.remove("has-record");
                }
            });
        })
        .catch(error => console.error("Error fetching monthly count:", error));
    }


    //カレンダの操作のクリックイベント
    prevNextIcon.forEach(icon => {
        icon.addEventListener("click",() =>{
            currMonth = icon.id === "prev" ? currMonth - 1: currMonth + 1;
            
            //年の調整する
            if (currMonth < 0){
                currMonth = 11;
                currYear -= 1;
            }else if (currMonth > 11){
                currMonth = 0;
                currYear += 1;
            }
            renderCalendar();
            updateMonthlyCount();
        });
    });

    //BIG3を表示する
    const updateBig3Max = () => {
        fetch("counter/big3max", {
            method: "POST",
            headers: { "Content-Type": "application/json" }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Big 3 Max Data:", data);
            if (!data.error) {
                document.querySelector(".CurBig3").innerText = 
                    `BIG 3 : ${data.big3Total} kg `;
            } else {
                console.error("Error fetching Big 3 Max data:", data.error);
            }
        })
        .catch(error => console.error("Error fetching Big 3 max total:", error));
    };
    
    updateBig3Max();

    //削除機能
    function deleteWorkout(date) {
        if(!date){
            alert("日付を選択してください");
            return;
        }

        if(confirm(`${date}のトレーニング記録を削除しますか？`)){
            fetch("training/delete",{
                method: "POST",
                headers:{"Content-type":"application/x-www-form-urlencoded"},
                body:`date=${encodeURIComponent(date)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert("削除しました！");
                    document.querySelector(".menu-wrapper").innerHTML = ""; 
                    document.getElementById("training-log").innerText = "データがありません";
                } else {
                    alert("削除に失敗しました：" + data.message);
                }
            })
        }
    }

    //ログの日付を取得
    function getFormattedDateFromDateLog(){
        const dateLogElement = document.getElementById("date-log");
        if(!dateLogElement){
            console.error("not found");
            return null;
        }

        const dateText = dateLogElement.innerText.trim();
        const match = dateText.match(/^(\d+)月(\d+)日$/);

        if(!match){
            console.error("invalid format:",dateText);
            return null;
        }

        let month = match[1].padStart(2,'0');
        let day = match[2].padStart(2,'0');

        let formattedDate = `2025-${month}-${day}`;
        console.log("Formatted Date:", formattedDate);
        return formattedDate;

    }

    document.querySelector(".log_delete").addEventListener("click", (event) => {
        let formattedDate = getFormattedDateFromDateLog();
        if(formattedDate){
            deleteWorkout(formattedDate);
        }        
    });

     
    });
    
