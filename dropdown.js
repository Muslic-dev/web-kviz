const selected = document.querySelector(".selected");
const optionsContainer = document.querySelector(".options-container");
const optionsList = document.querySelectorAll(".option");
const arrow = document.querySelector("#arrow");
let isArrowClicked = false;

selected.onfocus = ()=> 
{   
    selected.style.backgroundColor = "lightgray";
    optionsContainer.classList.toggle("active");
    if(arrow.classList.contains("bi-arrow-down-short"))
    {
        arrow.classList.remove("bi-arrow-down-short");
        arrow.classList.add("bi-arrow-up-short");
    }
}

arrow.addEventListener('mousedown', function(e)
{
    e.preventDefault();
    isArrowClicked = true;
})

arrow.addEventListener('click', function(e)
{
    const isMenuOpen = optionsContainer.classList.contains("active")
    if(isMenuOpen)
    {
        selected.blur();
    }
    else
    {
        selected.focus();
    }
    isArrowClicked = false;
})

selected.onblur = ()=> 
{
    selected.style.backgroundColor = "white";
    optionsContainer.classList.toggle("active");
    if(arrow.classList.contains("bi-arrow-up-short"))
    {
        arrow.classList.remove("bi-arrow-up-short");
        arrow.classList.add("bi-arrow-down-short");
    }
}

optionsList.forEach((item)=> (item.onclick = ()=>
{
    console.log(item);
    console.log(item.innerHTML);
    console.log(item.innerText);
    selected.value = item.innerText;
}))