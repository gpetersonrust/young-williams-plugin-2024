export default class Moxcar_Dynamic_Scroll {
    constructor(elementToWatch, elements, callback) {
      this.elementToWatch = elementToWatch;
      this.elements = elements;
      this.callback = callback;
      this.paginationLimit = 8;
      this.index = 0;
      this.scroll();
    }
  
    scroll() {
      const handleScroll = () => {
        const elementToWatchRect = this.elementToWatch.getBoundingClientRect();
        const elementToWatchBottom = elementToWatchRect.bottom;
        const windowHeight = window.innerHeight + 400;
  
        if (elementToWatchBottom <= windowHeight) {
          this.callback(this.elementToWatch, this.elements, this.paginationLimit, this.index);
          this.index++;
        }
      };
  
      window.addEventListener('scroll', handleScroll);
      handleScroll(); // Check initially
  
      // Optional: Clean up the event listener when the instance is destroyed
      // e.g., in a componentWillUnmount method if you are using React
      // window.removeEventListener('scroll', handleScroll);
    }
  }