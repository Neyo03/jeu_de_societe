import axios from "axios";
import { useState, useEffect } from "react";

  export function fetchItems(url) {
    const [loading, setLoading] = useState(false);
    const [items, setItems] = useState([]);

    const load = useEffect(async ()=>{
      console.log('salut');
      setLoading(true)
      const response = await axios({
        method: 'GET',
        url: url
      })
      
      const responseData = await response.data;
     
      if (response.status == 200) {
        setItems(responseData);
      }else{
        console.error('error : '+ responseData);
      }
      setLoading(false);
    },[url])

    
    return {
      setItems,
      items,
      loading,
      load
    }
}

