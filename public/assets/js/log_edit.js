document.addEventListener("DOMContentLoaded",() =>{

    const addEditListeners = () =>{
      document.querySelectorAll(".log_edit").forEach(button =>{
        button.addEventListener("click",(event) => {
            const setElement = event.target.closest(".set");

            setElement.querySelector(".weight").classList.add("hidden");
            setElement.querySelector(".reps").classList.add("hidden");
            setElement.querySelector(".log_delete").classList.add("hidden");
            setElement.querySelector(".log_edit").classList.add("hidden");

            setElement.querySelector(".edit-weight").classList.add.remove("hidden");
            setElement.querySelector(".edit-reps").classList.add.remove("hidden");
            setElement.querySelector(".log_save").classList.add.remove("hidden");
            setElement.querySelector(".log_cancel").classList.add.remove("hidden");

            event.target.classList.add("hidden");
        });

    });  
    }
   
    

});