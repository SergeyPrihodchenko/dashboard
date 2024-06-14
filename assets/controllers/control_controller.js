import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'form', 'submit' ]

    initialize() { }

    connect() {    }

    disconnect() {    }

    selectSite() {
        const form = this.formTarget
        form.submit()
    }

    selectProgram(event) {
        const selector = document.getElementById('control_program')
        const form = this.formTarget

        selector.setAttribute('value', event.target.value)
        form.submit()
    }

    selectDate(event) {
        const selector = document.getElementById('control_program')
        const form = this.formTarget

        selector.removeAttribute('value')
        form.submit()
    }
}
