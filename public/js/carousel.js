
document.addEventListener("DOMContentLoaded", () => {
  const slidesContainer = document.getElementById("carousel-slides");
  const slides = slidesContainer.children;
  const prevButton = document.getElementById("prev-button");
  const nextButton = document.getElementById("next-button");
  const indicators = document.querySelectorAll(".indicator");

  let currentIndex = 0;


  // Fonction pour mettre à jour le slide actif
  function updateCarousel() {
    slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
    
    // Mise à jour des indicateurs
    indicators.forEach((indicator, index) => {
      indicator.classList.toggle("bg-gray-800", index === currentIndex);
      indicator.classList.toggle("bg-gray-300", index !== currentIndex);
    });
  }

  // Navigation au slide précédent
  prevButton.addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    updateCarousel();
    console.log(currentIndex);
  });
  
  // Navigation au slide suivant
  nextButton.addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % slides.length;
    updateCarousel();
    console.log(currentIndex);
  });

  // Navigation via les indicateurs
  indicators.forEach((indicator, index) => {
    indicator.addEventListener("click", () => {
      currentIndex = index;
      updateCarousel();
    });
  });

  // Initialisation
  updateCarousel();
});
