import {
  postData,
  numberFormat,
  divLoading,
  selectClientDOM,
  resetUI,
} from "../../helpers/helpers.js";

let tokenClient = null;
let baseUrl = "https://app.samycosmetics.com/api-samy/public/api/";

const fetchOrder = () => {
  const orderID = document
    .querySelector(".slide--active")
    .getAttribute("data-slide");
  window.location.href = `https://app.samycosmetics.com/order-status/view/order/order.html?orderID=${orderID}`;
};

const fetchWallet = () => {
  window.location.href = `https://app.samycosmetics.com/order-status/view/wallet/wallet.html?token=${tokenClient}&bot=1`;
};

const closeBrowser = () => {
  window.open("about:blank", "_self");
  window.close();
};

const getStatus = async () => {
  // console.log('Here');
  document.getElementById("status").classList.add("hidden");
  divLoading("progress-bar", true);

  const orderID = document
    .querySelector(".slide--active")
    .getAttribute("data-slide");
  // const url = "controller/router.php";
  // const data = {
  //   id: orderID,
  //   case: "getStatusOrder",
  // };
  // const response = await postData(url, data, true);
  const url = `${baseUrl}Orders/${orderID}`;
  const response = await (
    await fetch(url, {
      headers: {
        accept: "application/json",
      },
    })
  ).json();
  divLoading("progress-bar", false);

  console.log(response);

  document.getElementById("status").classList.remove("hidden");
  document.querySelector(".release-date").textContent = "";
  document.querySelector(".state-1").textContent = "";
  document.querySelector(".state-2").textContent = "";
  document.querySelector(".state-3").textContent = "";
  document.querySelector(".state-4").textContent = "";
  document.querySelector(".release_order").textContent = "";
  document.querySelector(".date_start").textContent = "";
  document.querySelector(".text_barra").textContent = "";
  document.querySelector(".text_deliver").textContent = "";

  response.forEach((index) => {
    const status = index.status;
    const releaseDate = index.release_date;
    const remittance = index.remittance;
    const releaseOrder = index.release_order;
    const deliverStart = index.deliver_start;
    const deliverDate = index.deliver_end;

    let one = document.querySelector(".one");
    let two = document.querySelector(".two");
    let three = document.querySelector(".three");
    let four = document.querySelector(".four");

    if (releaseDate != null) {
      const state1 = document.querySelector(".state-1");
      const date = document.querySelector(".release-date");
      date.append(releaseDate);
      state1.innerHTML = "Recibido";

      one.classList.add("active");
      two.classList.remove("active");
      three.classList.remove("active");
      four.classList.remove("active");
    }

    if (status != null) {
      one.classList.add("active");
      two.classList.add("active");
      three.classList.remove("active");
      four.classList.remove("active");

      const nameOrder = document.querySelector(".state-2");
      const date = document.querySelector(".release_order");

      nameOrder.append(status);
      date.append(releaseOrder);
    }

    if (remittance != null) {
      one.classList.add("active");
      two.classList.add("active");
      three.classList.add("active");
      four.classList.remove("active");

      const stateText = document.querySelector(".state-3");
      stateText.innerHTML = "Despachado";

      // const numberRemittance = document.querySelector('.guide');
      // numberRemittance.innerHTML= `Guia: ${remittance}`

      const dateStart = document.querySelector(".date_start");
      dateStart.append(deliverStart);
    }

    if (deliverDate != null) {
      one.classList.add("active");
      two.classList.add("active");
      three.classList.add("active");
      four.classList.add("active");

      const stateText = document.querySelector(".text_deliver");
      stateText.innerHTML = "Entregado";

      const dateTake = document.querySelector(".state-4");
      dateTake.append(deliverDate);
    }
  });
};

const infoWalllet = async (client = null) => {
  const url = `${baseUrl}Wallet/Client?token=${client}`;
  const response = await (await fetch(url)).json();
  const table = document.getElementById("data-wallet");
  // const name = document.querySelector(".Nombre");
  let html = "";
  const item = response;

  // name.innerHTML = formatName(item.nombre);
  html += `
    <tr>
      <td>${numberFormat(item.limite)}</td>
      <td>${numberFormat(item.deuda)}</td>
      <td>${numberFormat(item.dia_atraso)}</td>
      <td>${numberFormat(item.cupo_disp - item.total_pedido)}</td>
    </tr>
  `;

  table.innerHTML = html;
};

let formatName = (fullName) => {
  const nameParts = fullName.split(" ");
  const formattedParts = nameParts.map(
    (part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase()
  );
  return formattedParts.join(" ");
};

const nextSlide = () => {
  let activeSlide = document.querySelector(".slide--active");
  let nextSlide = activeSlide.nextElementSibling;
  if (nextSlide) {
    activeSlide.classList.remove("slide--active");
    nextSlide.classList.remove("next");
    nextSlide.classList.add("slide--active");
    renderSlides();
    renderBtns();
    getStatus();
  }
};

const renderBtns = () => {
  let nextBtn = document.querySelector("#forward");
  let prevBtn = document.querySelector("#back");

  let activeSlide = document.querySelector(".slide--active");
  let prevSlide = activeSlide.previousElementSibling;
  !prevSlide
    ? prevBtn.classList.add("disabled")
    : prevBtn.classList.remove("disabled");

  let nextSlide = activeSlide.nextElementSibling;
  !nextSlide
    ? nextBtn.classList.add("disabled")
    : nextBtn.classList.remove("disabled");
};

const prevSlide = () => {
  let activeSlide = document.querySelector(".slide--active");
  let prevSlide = activeSlide.previousElementSibling;
  if (prevSlide) {
    activeSlide.classList.remove("slide--active");
    prevSlide.classList.remove("prev");
    prevSlide.classList.add("slide--active");
    renderSlides();
    renderBtns();
    getStatus();
  }
};

const renderSlides = () => {
  let slides = document.querySelectorAll(".slide");
  if (!slides) {
    return;
  }
  let activeSlide = document.querySelector(".slide--active");
  if (!activeSlide) {
    activeSlide = slides.item(0);
    activeSlide.classList.add("slide--active");
  }
  [].forEach.call(slides, function (slide) {
    slide.classList.remove("prev", "next");
  });
  let prevSlide = activeSlide.previousElementSibling;
  prevSlide && prevSlide.classList.add("prev");

  let nextSlide = activeSlide.nextElementSibling;
  nextSlide && nextSlide.classList.add("next");

  // document.querySelector('.slide--active').addEventListener('click',getStatus, { once: true })
};

const renderSlider = (element) => {
  const slider = document.querySelector(element);
  if (slider) {
    let nextButton = document.querySelector("#forward");
    nextButton.addEventListener("click", () => {
      nextSlide();
    });

    let prevButton = document.querySelector("#back");
    prevButton.addEventListener("click", () => {
      prevSlide();
    });
    renderSlides();
  }
};

const showCardMessage = (slideData) => {
  const cardMessage = document.createElement("div");
  cardMessage.className = "card-message";
  const message = document.createElement("h1");
  const image = document.createElement("img");
  image.src = "assets/img/void-box.svg";
  message.textContent = slideData.message;
  document.querySelector("body").append(cardMessage);
  document.querySelector("body").classList.add("center-items");
  document.querySelector(".main").style.display = "none";
  cardMessage.appendChild(message);
  cardMessage.appendChild(image);
  document.querySelector(".section-2").style.display = "none";
};

// const selectClient = (data) => {
//   if (data.hasMany == undefined) {
//     document.querySelector("#section-2-id").classList.remove("display-none");
//   }
// };

const createSlider = async (client = null) => {
  resetUI([{ target: document.querySelector("#section-1-id"), type: "HTML" }]);
  tokenClient = client;
  const url = `${baseUrl}Orders?token=${client}`;
  const slideData = await (await fetch(url)).json();
  document.querySelector("body").classList.remove("display-none");

  if (slideData.status == 404) {
    showCardMessage(slideData);
    return false;
  }

  infoWalllet(client);
  document.querySelector("#section-2-id").classList.remove("display-none");
  document.querySelector("#section-1-id").classList.remove("display-none");

  if (slideData == "") {
    const messageError = document.querySelector(".mensaje-error");
    const controlButton = document.querySelector(".container-buttons");
    messageError.style.display = "block";
    // controlButton.style.display = "none";
  } else {
    const container = document.createElement("div");
    container.classList.add("container");

    const slider = document.createElement("div");
    slider.classList.add("slider");

    let activeSlideIndex = 0;

    let quantityOrders = 0;
    slideData.forEach((data, index) => {
      const slide = document.createElement("div");
      slide.classList.add("slide");

      const sort = document.createElement("div");
      sort.classList.add("slide-center");

      const image = document.createElement("img");
      image.src = "assets/img/LOGO-SAMY-BLANCO-ESTEFA.png";
      image.classList.add("slide-image");
      slide.appendChild(image);

      const slideText = document.createElement("span");
      slideText.textContent = `Pedido: ${data.n_order}`;
      slideText.classList.add("slide-text");

      const slideDate = document.createElement("span");
      slideDate.innerHTML = `${data.date_release}`;
      slideDate.classList.add("slide-small");

      let statusText = document.createElement("span");

      if (data.status != null) {
        statusText.append(data.status);
        statusText.classList.add("slide-small");
      }
      if (data.deliver != null) {
        statusText.innerHTML = "ENTREGADO";
        statusText.classList.add("slide-small");
      }

      sort.append(slideText);

      slide.append(sort);
      slide.setAttribute("data-slide", data.n_order.toString());

      slider.appendChild(slide);

      if (index === slideData.length - 1) {
        activeSlideIndex = index;
      }

      quantityOrders++;
    });

    slider.children[activeSlideIndex].classList.add("slide--active");

    // const controls = document.createElement("div");
    // controls.classList.add("controls");

    // const controlData = [
    //   { id: "back", class: "uil-arrow-left" },
    //   { id: "forward", class: "uil-arrow-right" },
    // ];

    const previousControl = document.createElement("div");
    previousControl.className = "uil-arrow-left";
    previousControl.setAttribute("id", "back");

    const nextControl = document.createElement("div");
    nextControl.className = "uil-arrow-right";
    nextControl.setAttribute("id", "forward");

    // controlData.forEach((data) => {
    //   const control = document.createElement("div");
    //   control.setAttribute("id", data.id);
    //   control.setAttribute("class", data.class);

    //   controls.appendChild(control);
    // });

    if (quantityOrders == 1) {
      previousControl.classList.add("no-visible");
      nextControl.classList.add("no-visible");
    }

    container.appendChild(previousControl);

    container.appendChild(slider);

    container.appendChild(nextControl);
    // container.appendChild(controls);

    const section = document.querySelector(".section-1");
    section.append(container);

    renderSlider(".slider");
    getStatus();
    return true;
  }
};

const getClients = async () => {
  // let response = { hasManyClients: false, client: null };
  const searchParams = new URLSearchParams(window.location.search);
  if (searchParams.has("clientSelected")) return true;
  const url = `${baseUrl}ContactClients?token=${searchParams.get("token")}`;

  const responseClients = await (
    await fetch(url, {
      headers: {
        accept: "application/json",
      },
    })
  ).json();
  // if (responseClients.length > 1) {
  selectClientDOM(responseClients, createSlider);
  // response.hasManyClients = true;
  // }

  // response.client = responseClients[0].CLIENTE;
  // return response;
};

document.addEventListener("DOMContentLoaded", async () => {
  getClients();
  // const manyClients = await getClients();
  // if (manyClients.hasManyClients) return;

  // createSlider(manyClients.client);
  // if (!createSlider()) return;

  // return;
  // infoWalllet();
});

document.querySelector(".link-detalle").addEventListener("click", fetchOrder);
document.querySelector(".link-salir").addEventListener("click", closeBrowser);
document.querySelector(".link-cartera").addEventListener("click", fetchWallet);
document.getElementById("status").classList.add("hidden");

const enlacesSalir = document.querySelectorAll(".button");

enlacesSalir.forEach((enlace) => {
  enlace.addEventListener("click", (event) => {
    event.preventDefault();
  });
});
