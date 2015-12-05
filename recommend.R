###########################################################################################################################################################
#Purpose : Perform Jaccard similarity on the test data row in sampleinput.csv and write recommend medications to a file called RecommendedMedicationss
#Input   : input.csv (contains demographics and diagnosis details of the patient)
#Output  : riskandrecommendations.csv ( contains 1. Demographics 2. Diagnosis details 3. Medication Recommendations 4. Risk Scores w/ and w/o Medications )
###########################################################################################################################################################

suppressMessages(library(stringdist))

rulesdf = read.csv('rules.csv')
medlookup = read.csv('MedicationsToConsider.csv')

RecommendedMedications = c()

#input.csv : 1-51.attributes 52.EpicPatientID  
#rules.csv : 1-51.attributes 52-101.medications 101.RiskScoreWithoutMedications 102.RiskScoreWithMedications
mytestrow = read.csv('input.csv')[1,]
matchrow = rulesdf[which.min(Reduce(`+`,Map(stringdist,rulesdf[,1:75], mytestrow[,1:75], method='jaccard'))),]

RecommendedMedications = c()

#from 53rd column onwards, it's MED
for(j in 73:122)
{
        if(matchrow[j]==1)
        {
                if((length(RecommendedMedications))==0)
                {
                        RecommendedMedications = as.character(medlookup[which(medlookup$CODE==names(matchrow)[j]),"DESCRIPTION"])
                }else
                {
                        RecommendedMedications = paste(RecommendedMedications,as.character(medlookup[which(medlookup$CODE==names(matchrow)[j]),"DESCRIPTION"]),sep="||")
                }
        }
}

mytestrow$RecommendedMedications = RecommendedMedications;
mytestrow$RiskScoreWithoutMedications = round(matchrow$RiskScoreWithoutMedications)
mytestrow$RiskScoreWithMedications = round(matchrow$RiskScoreWithMedications)

print(RecommendedMedications);
write.csv(mytestrow,'riskandrecommendations.csv',row.names=FALSE)
