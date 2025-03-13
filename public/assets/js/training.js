document.addEventListener("DOMContentLoaded",function(){
    

    //selection of type of training
    document.querySelector(".nav-button").forEach(button=>{
        button.addEventListener("click", function() {
            const selectedCategory = this.getAttribute("data-category");
            document.getElementById("selected-training").value = selectedCategory;
        });
    });

});