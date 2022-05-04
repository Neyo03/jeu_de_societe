import axios from "axios";
import React from 'react';
import { render, unmountComponentAtNode } from 'react-dom';
import { useEffect } from 'react';
import { fetchItems } from '../Hooks/FetchItems';
import Message from './Message';
import { useState } from "react";
import { createRef } from "react";


const Messages = ({uuid, user}) => {

    const {setItems ,items ,loading, load} = fetchItems('/read/message/discussion/'+uuid)
    const [messages, setMessages] = useState([]);
    const message_form_edit =  document.getElementById('Message_send_form');
    const params = new URLSearchParams();
    const [time, setTime] = useState(Date.now());

    
    // var ONE_MINUTE = 10 * 1000;

    // function repeatEvery(func, interval) {
    //     // Check current time and calculate the delay until next interval
    //     var now = new Date(),
    //         delay = interval - now % interval;

    //     function start() {
    //         // Execute function now...
    //         func
    //         // ... and every interval
    //         setInterval(fetchItems('/read/message/discussion/'+uuid), interval);
    //     }

    //     // Delay execution until it's an even interval
    //     setTimeout(start, delay);
    // }
    // repeatEvery(fetchItems('/read/message/discussion/'+uuid),ONE_MINUTE)
    


    useEffect(()=>{
        setMessages(items)
        
        // const interval = setInterval(() => setTime(Date.now()), 1000);
        // return () => {
            
        //     clearInterval(interval);
        // };
        
        
    }, [items])


    message_form_edit.onsubmit = e =>{

        
        e.preventDefault();
        let message = {
            authorId: user.id,
            authorPseudo: user.pseudo,
            content: e.target[0].value,
            // id: list[list.length-1].id + 1,
        }

        let list = [message,...messages];
       
    
        
        


        let data = [
            uuid,
            e.target[0].value
        ]

        for (let index = 0; index < data.length; index++) {
            const element = data[index];
            params.append(index, element);
        }

        const response = axios({
            method: 'POST',
            url: '/send/message/discussion',
            data : params
        })

        e.target.children[0].value = ""
    
        setMessages(list)
        
    }
    console.log(messages);
    return (
        <div className="Message_list">
           {loading && <div>Chargement</div>}
            {
                messages.map((message, index)=>(
                    <Message message={message} key={index} user={user} />
                   
                ))
            }
            
        </div>
    );
};
export default Messages;