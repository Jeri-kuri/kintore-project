document.addEventListener("DOMContentLoaded",() =>{

    const updateMonthlyCount = () =>{
        const year = currYear;
        const month = (currMonth + 1).toString().padStart(2, '0');

        fetch("category/getMonthlyCount", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `year=${year}&month=${month}`
        })
        .then(response => response.json())
        .then(data => {
            console.log("Monthly count:", data.count);
            document.querySelector(".counter").innerText = `${data.count}回`;
        })
        .catch(error => console.error("Error fetching monthly count:", error));
    }
})