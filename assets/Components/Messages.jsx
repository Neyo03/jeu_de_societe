import React from 'react';
import { render, unmountComponentAtNode } from 'react-dom';
import { useEffect } from 'react/cjs/react.production.min';
import { fetchItems } from '../Fetch/FetchItems';
import Message from './Message';

const Messages = ({uuid, user}) => {

    const {items : messages, load, loading} = fetchItems('/read/message/discussion/'+uuid)

    // useEffect(()=>{
    //     load
    // },[])
    return (
        <div>
           {loading && 'Chargement'}
            {
                messages.map((message, index)=>(
                    <Message message={message} key={index} user={user} />
                ))
            }
           load
        </div>
    );
};
export default Messages;