import {useState, useEffect, useRef} from '@wordpress/element';

export const useBreakpointWidth = ({width, node, className = 'wc-ppcp-sm__container'}) => {
    const [windowWidth, setWindowWidth] = useState(window.innerWidth);
    const [element, setElement] = useState(node);
    useEffect(() => {
        setElement(node);
    }, [node]);

    useEffect(() => {
        const handleResize = () => setWindowWidth(window.innerWidth);
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    useEffect(() => {
        if (element) {
            if (element.clientWidth <= width) {
                element.classList.add(className);
            } else {
                element.classList.remove(className);
            }
        }
    }, [windowWidth, width, element]);
}