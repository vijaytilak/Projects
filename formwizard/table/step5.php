    <button style="height:28px; float:right" class="mybutton" type="button" id="UseRecommendedButtonStep4">Use Recommended Values</button><br /><br />

<table width="100%" id="configcontainer" class="pure-table" border="0" cellspacing="0" cellpadding="3">
      <caption>
        Other Configuration
      </caption>
      <tr>
      <td style="background:#E2E0FF; width:40%"><label for="adf">ADF:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="adf" value="0" id="adf_0" checked/>No</label> 
        <label><input type="radio" name="adf" value="1" id="adf_1" />Yes</label>
      </td>
      </tr>
      <tr>
      <td style="background:#E2E1FF"><label for="wq">WQ:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="wq" value="0" id="wq_0" checked/>No</label> 
        <label><input type="radio" name="wq" value="1" id="wq_1" />Yes</label>
      </td>
      </tr>
      <tr>
      <td style="background:#E2E1FF"><label for="fio">FIO:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="fio" value="0" id="fio_0" checked/>No</label> 
        <label><input type="radio" name="fio" value="1" id="fio_1" />Yes</label>
      </td>
      </tr>
      <tr>

      <td style="background:#E2E1FF"><label for="buildingconsent">Building Consent:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="buildingconsent" value="0" id="buildingconsent_0" checked/>No</label> 
        <label><input type="radio" name="buildingconsent" value="1" id="buildingconsent_1" />Yes</label>
      </td>
      </tr>
      <tr>
      <td style="background:#E2E1FF"><label for="gst">GST:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="gst" value="0" id="gst_0" checked/>No</label> 
        <label><input type="radio" name="gst" value="1" id="gst_1" />Yes</label>
      </td>
      </tr>
      <tr>
      <td style="background:#E2E1CF"><label for="hotdipgalv">Hot Dip Galvanizing:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="hotdipgalv" value="0" id="hotdipgalv_0" checked/>No</label> 
        <label><input type="radio" name="hotdipgalv" value="1" id="hotdipgalv_1" />Yes</label>
      </td>
      </tr> 
      <tr>
      <td style="background:#E2E1CF"><label for="epoxy">Epoxy Primer:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="epoxy" value="0" id="epoxy_0" checked/>No</label> 
        <label><input type="radio" name="epoxy" value="1" id="epoxy_1" />Yes</label>
      </td>
      </tr> 
      <tr>
      <td style="background:#E2E1CF"><label for="polyester">Polyester:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="polyester" value="0" id="polyester_0" checked/>No</label> 
        <label><input type="radio" name="polyester" value="1" id="polyester_1" />Yes</label>
      </td>
      </tr> 
      <tr>
      <td style="background:#E2E1CF"><label for="abcite">Abcite:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="abcite" value="0" id="abcite_0" checked/>No</label> 
        <label><input type="radio" name="abcite" value="1" id="abcite_1" />Yes</label>
      </td>
      </tr> 
      <tr>
      <td style="background:#E2E1FF"><label for="leanto">Lean To:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="leanto" value="0" id="leanto_0" checked/>No</label> 
        <label><input type="radio" name="leanto" value="1" id="leanto_1" />Yes</label>
      </td>
      </tr>      
    </table>    
        <input type="hidden" name="quoteaction" value="servervalidatestep5" id="quoteaction" />
    <input type="hidden" name="quoteid" value="<?php echo $quoteid; ?>" id="quoteid" />
