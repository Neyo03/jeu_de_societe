import React from 'react';

const Message = ({message, user}) => {
   console.log();
    return (

        <div className={`${user.id == message.authorId ? "Message_current_user" : "Message_other_user"  }`} >
            
            {/* <p> {message.authorPseudo}</p> */}
            <p>
                {message.content}
                {
                    message.messageParticipants.forEach(messageStatus => {
                        
                        if (messageStatus.participant.id != user.id) {
                            if (messageStatus.status == 1 ) {
                                "vu"  
                            }
                            else if (messageStatus.status == 0 ) {
                                "non vu"
                            }
                        }
                    })
                }
            </p>
            
        </div>
    );
};

export default Message;