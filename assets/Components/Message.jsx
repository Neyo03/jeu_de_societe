import React from 'react';

const Message = ({message, user}) => {
   
    return (
        
        <div style={user == message.authorId ? {background :'#333'} : {background : '#fff'} }>
            <p> {message.authorPseudo}</p>
            <p>
                {message.content}
            </p>
            
        </div>
    );
};

export default Message;