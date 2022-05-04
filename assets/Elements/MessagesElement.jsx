
import React from 'react';
import { render, unmountComponentAtNode } from 'react-dom';
import Messages from '../Components/Messages';

// export default Message;
class MessagesElement extends HTMLElement {

    connectedCallback(){
       render(<Messages uuid={this.attributes['data-discussion'].value} user={{ id :this.attributes['data-user'].value, pseudo : this.attributes['data-user-pseudo'].value}} />, this);
    }
    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}
customElements.define('discussion-message', MessagesElement)