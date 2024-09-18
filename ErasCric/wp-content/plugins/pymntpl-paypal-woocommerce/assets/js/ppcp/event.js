import {createHooks} from "@wordpress/hooks";

class Event {

    constructor() {
        this.hooks = createHooks();
    }

    on(event, callback, priority = 10) {
        this.hooks.addAction(event, 'wcPPCP', callback, priority);
    }

    trigger(event, ...args) {
        this.hooks.doAction(event, ...args);
    }

    addFilter(event, callback, priority = 10) {
        this.hooks.addFilter(event, 'wcPPCP', callback, priority);
    }

    applyFilters(event, ...args) {
        return this.hooks.applyFilters(event, ...args);
    }

}

export default Event;